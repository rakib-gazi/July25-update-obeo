<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\HotelInvoice;
use App\Models\HotelInvoiceRoom;
use App\Models\InvoicedReservation;
use App\Models\PaymentMethod;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class HotelInvoiceController extends Controller
{
//    hotel invoice main page
    function hotelInvoice( Request $request )
    {
        return Inertia::render('HotelInvoice');
     }
    function createInvoice(Request $request)
    {
        $data = $request->all();
        // Numeric-only: YYYYMMDD + 4-digit random number
        $prefix = now()->format('Ymd'); // e.g. 20250527
        $random = str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT); // e.g. 0384
        $hotel_inv = (int)($prefix . $random); // e.g. 202505270384
        $hotel_inv_date = Carbon::now()->format('Y-m-d');
        DB::beginTransaction();
        try {
            $hotelInvoice = HotelInvoice::create([
                'hotel_id' => $data['hotel_id'],
                'inv_no' => $hotel_inv,
                'inv_date' => $hotel_inv_date,
                'total_amount' => $data['total_amount'],//firstly total amount of booking then if adjustment happen then after adjustment final amount
                'total_advance' => $data['total_advance'],
                'currency_id' => $data['advanceCurency']
            ]);
            $invoicedReservation = InvoicedReservation::create([
                'reservation_id' => $data['reservation_id'],
                'hotel_invoice_id' => $hotelInvoice->id,
            ]);
            foreach ($data['rooms'] as $roomData) {
                $room = HotelInvoiceRoom::create([
                    'hotel_invoice_id' => $hotelInvoice->id,
                    'room_name' => $roomData['name'],
                    'total_room' => $roomData['total_room'],
                    'total_price' => $roomData['total_price'],
                    'currency_id' => $roomData['currency_id'],
                    'exchange_rate' => $data['exchange_rate'],
                    'commission_type' => $data['commission_type'],
                    'commission_value' => $data['commission_value'],
                ]);
            }
//            Log::info('Invoice information', [
//                'hotel_id' => $data['hotel_id'],
//                'inv_no' => $hotel_inv,
//                'inv_date' => $hotel_inv_date,
//                'total_amount' => $data['total_amount'],
//                'total_advance' => $data['total_advance'],
//                'currency_id' => $data['advanceCurency'],
//                'reservation_id'   => $data['reservation_id'],
//                'rooms'         => $data['rooms'],
//                'hotel_invoice_id' => $hotelInvoice->id,
//            ]);
            DB::commit();

            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            Log::info('Error message', [
                'error' => $e->getMessage()
            ]);
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    function getAllHotelInvoices(Request $request)
    {
        $hotelNames = Hotel::select('hotels.id','hotels.hotelName')
            ->join('hotel_invoices', 'hotels.id', '=', 'hotel_invoices.hotel_id')
            ->join('invoiced_reservations', 'hotel_invoices.id', '=', 'invoiced_reservations.hotel_invoice_id')
            ->distinct()
            ->get();
        return Inertia::render('AllHotelInvoices', [
            'hotelNames' => $hotelNames,
        ]);
    }
    public function getHotelInvoicesByHotel(Request $request, $hotelId)
    {
        $invoices = HotelInvoice::with([
            'hotel:id,hotelName,hotelAddress',
            'invoicedReservation.reservation:id,status_id,reservation_no,check_in,check_out,guest_name,rate_id,source_id,payment_method_id',
            'hotelInvoiceRooms:id,room_name,total_room,total_price,currency_id,exchange_rate,commission_type,commission_value,hotel_given_price,hotel_invoice_id'
        ])
            ->where('hotel_id', $hotelId)
            ->select('id', 'inv_no', 'inv_date', 'total_amount', 'total_advance', 'currency_id', 'hotel_id')
            ->get();

        $result = $invoices->map(function ($invoice) {
            $reservation = $invoice->invoicedReservation?->reservation;

            return [
                'id' => $invoice->id,
                'inv_no' => $invoice->inv_no,
                'inv_date' => $invoice->inv_date,
                'total_amount' => $invoice->total_amount,
                'total_advance' => $invoice->total_advance,
                'currency_id' => $invoice->currency_id,
                'hotelName' => $invoice->hotel->hotelName,

                'hotel' => $invoice->hotel ? [
                    'id' => $invoice->hotel->id,
                    'hotelName' => $invoice->hotel->hotelName,
                    'hotelAddress' => $invoice->hotel->hotelAddress,
                ] : null,

                'reservation' => $reservation ? [
                    'id' => $reservation->id,
                    'status_id' => $reservation->status_id,
                    'reservation_no' => $reservation->reservation_no,
                    'check_in' => $reservation->check_in,
                    'check_out' => $reservation->check_out,
                    'guest_name' => $reservation->guest_name,
                    'rate_id' => $reservation->rate_id,
                    'source_id' => $reservation->source_id,
                    'payment_method_id' => $reservation->payment_method_id,
                ] : null,

                'hotel_invoice_rooms' => $invoice->hotelInvoiceRooms->map(function ($room) {
                    return [
                        'id' => $room->id,
                        'room_name' => $room->room_name,
                        'total_room' => $room->total_room,
                        'total_price' => $room->total_price,
                        'currency_id' => $room->currency_id,
                        'exchange_rate' => $room->exchange_rate,
                        'commission_type' => $room->commission_type,
                        'commission_value' => $room->commission_value,
                        'hotel_given_price' => $room->hotel_given_price,
                    ];
                })->values(),
            ];
        });

        // Group by checkout month using YYYY-MM as key
        $grouped = $result->groupBy(function ($invoice) {
            return Carbon::parse($invoice['reservation']['check_out'])->format('Y-m');
        });

        // Sort by the YYYY-MM key so months are in chronological order
        $grouped = $grouped->sortKeys();

        // Get the hotel name from first record
        $hotelName = $result->first()['hotelName'] ?? null;

        // Format grouped months with "F Y" for display
        $months = $grouped->map(function ($items, $ym) {
            return [
                'month' => Carbon::createFromFormat('Y-m', $ym)->format('F Y'),
                'data' => $items->values()
            ];
        })->values();

        // Final structure
        $final = [
            'hotel' => $hotelName,
            'data' => $months
        ];


        return Inertia::render('InvoicesByHotel', [
            'invoicesByHotel' => $final,
        ]);
        //return response()->json($final);
    }



}
