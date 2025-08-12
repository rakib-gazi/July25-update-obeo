<?php

namespace App\Http\Controllers;

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
                'total_amount' => $data['total_amount'],
                'total_advance' => $data['total_advance'],
                'currency_id' => $data['advanceCurency']
            ]);
            Log::info('Invoice information', [
                'reservation_id'   => $data['reservation_id'],
                'currency_id'      => $data['advanceCurency'],
                'total_amount'     => $data['total_amount'],
                'total_advance'    => $data['total_advance'],
                'hotel_id'         => $data['hotel_id'],
                'rooms'         => $data['rooms'],
                'hotel_invoice_id' => $hotelInvoice->id,
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
                    'commission_type' => $roomData['commission_type'],
                    'commission_value' => $roomData['commission_value'],
                ]);
            }
//            DB::commit();

            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
