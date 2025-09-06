<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\HotelInvoice;
use App\Models\HotelInvoiceRoom;
use App\Models\InvoicedReservation;
use App\Models\MonthlyHotelInvoiceAdjustment;
use App\Models\PaymentMethod;
use App\Models\Reservation;
use App\Models\ReservationStatus;
use App\Models\Source;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;

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
        $sources = Source::get();
        $paymentMethod = PaymentMethod::get();
        $search = $request->input('search');
        $month = $request->input('month');
        $year  = $request->input('year');
        $showAll = filter_var($request->input('showAll'), FILTER_VALIDATE_BOOLEAN) ?? false;
//        log::info("Hotel Invoices by Hotel Id: $search,$month,$year,$showAll");
        // --- Base query with relationships ---
        $query = HotelInvoice::with([
            'hotel:id,hotelName,hotelAddress,commissionType,expediaCollectsCommission,hotelCollectsCommission',
            'invoicedReservation.reservation:id,status_id,reservation_no,check_in,check_out,guest_name,rate_id,source_id,payment_method_id',
            'invoicedReservation.reservation.source:id,source',
            'invoicedReservation.reservation.paymentMethod:id,payment',
            'hotelInvoiceRooms:id,room_name,total_room,total_price,currency_id,exchange_rate,commission_type,commission_value,hotel_given_price,hotel_invoice_id'
        ])
            ->where('hotel_id', $hotelId)
            ->select('id', 'inv_no', 'inv_date', 'total_amount', 'total_advance', 'currency_id', 'hotel_id');

        // --- Filter by month/year if showAll is false ---
        if (!$showAll) {
            // Normalize month/year
            if (empty($month) || $month === 'null') {
                $month = now()->subMonth()->month;
            }
            if (empty($year) || $year === 'null') {
                $year = now()->subMonth()->year;
            }

            $query->whereHas('invoicedReservation.reservation', function ($q) use ($month, $year) {
                $q->whereMonth('check_out', $month)
                    ->whereYear('check_out', $year);
            });
        }



        // --- Apply search ---
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('inv_no', 'like', "%$search%")
                    ->orWhere('inv_date', 'like', "%$search%")
                    ->orWhereHas('hotel', fn($qh) => $qh->where('hotelName', 'like', "%$search%"))
                    ->orWhereHas('invoicedReservation.reservation', function ($qres) use ($search) {
                        $qres->where('guest_name', 'like', "%$search%")
                            ->orWhere('reservation_no', 'like', "%$search%");
                    });
            });
        }

        $invoices = $query->get();

        // --- Map data for frontend ---
        $result = $invoices->map(function ($invoice) {
            $reservation = $invoice->invoicedReservation?->reservation;

            return [
                'id' => $invoice->id,
                'inv_no' => $invoice->inv_no,
                'inv_date' => $invoice->inv_date,
                'total_amount' => $invoice->total_amount,
                'total_advance' => $invoice->total_advance,
                'currency_id' => $invoice->currency_id,

                // Top-level hotel info
                'hotelName' => $invoice->hotel->hotelName ?? null,
                'hotel_id' => $invoice->hotel->id ?? null,
                'hotelAddress' => $invoice->hotel->hotelAddress ?? null,
                'commissionType' => $invoice->hotel->commissionType ?? null,
                'hotelCollectsCommission' => $invoice->hotel->hotelCollectsCommission ?? null,
                'expediaCollectsCommission' => $invoice->hotel->expediaCollectsCommission ?? null,

                // Top-level reservation info for easier search
                'reservation_id' => $reservation?->id,
                'guest_name' => $reservation?->guest_name,
                'reservation_no' => $reservation?->reservation_no,
                'status_id' => $reservation?->status_id,
                'check_in' => $reservation?->check_in,
                'check_out' => $reservation?->check_out,
                'rate_id' => $reservation?->rate_id,
                'source_id' => $reservation?->source_id,
                'source' => $reservation?->source?->source,
                'payment_method_id' => $reservation?->payment_method_id,
                'payment_method' => $reservation?->paymentMethod?->payment,

                // Nested objects (optional, can be used for detailed view)
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

        // --- Group by checkout month ---
        $grouped = $result->groupBy(function ($invoice) {
            return $invoice['check_out']
                ? \Carbon\Carbon::parse($invoice['check_out'])->format('Y-m')
                : 'Unknown';
        });

        $grouped = $showAll ? $grouped->sortKeysDesc() : $grouped->sortKeys();

        $hotelName = $result->first()['hotelName'] ?? null;

        $months = $grouped->map(function ($items, $ym) {
            return [
                'month' => $ym !== 'Unknown'
                    ? \Carbon\Carbon::createFromFormat('Y-m', $ym)->format('F Y')
                    : 'Unknown',
                'data' => $items->values()
            ];
        })->values();

        $final = [
            'success' => $hotelName !== null,
            'hotel' => $hotelName,
            'data' => $months
        ];
//        return  response() ->json($final);
        return Inertia::render('InvoicesByHotel', [
            'invoicesByHotel' => $final,
            'sources'  => $sources
        ]);
    }

    public function getInvoiceEligibleForUpdate()
    {

        $checkedInStatusId = ReservationStatus::where('status', 'Checked In')->value('id');

        $reservations = Reservation::where('status_id', $checkedInStatusId)
            ->whereHas('invoicedReservation') // must already have invoice
            ->with([
                'invoicedReservation.hotelInvoice.hotelInvoiceRooms',
                'user:id,fullName',
                'reservation_status:id,status',
                'hotel:id,hotelName,commissionType,expediaCollectsCommission,hotelCollectsCommission',
                'rate:id,rate',
                'currency:id,currency',
                'source:id,source',
                'paymentMethod:id,payment',
                'children:id,age',
                'rooms' => function ($query) {
                    $query->select('rooms.id', 'name', 'total_room', 'total_night', 'total_price', 'currency_id')
                        ->with('currency:id,currency');
                }
            ])
            ->get()
            ->filter(function ($reservation) {
                $invoice = $reservation->invoicedReservation->hotelInvoice ?? null;
                            Log::info('Invoice information', [
                'invoice' => $invoice
            ]);
                if (!$invoice) return false;

                $differences = [];

                // ✅ Compare reservation vs invoice top-level fields
                if ($reservation->total_advance != $invoice->total_advance) {
                    $differences['total_advance'] = [
                        'reservation' => $reservation->total_advance,
                        'invoice' => $invoice->total_advance,
                    ];
                }

                if ($reservation->currency_id != $invoice->currency_id) {
                    $differences['currency_id'] = [
                        'reservation' => $reservation->currency_id,
                        'invoice' => $invoice->currency_id,
                    ];
                }

                // ✅ Compare total price (rooms sum)
                $reservationTotal = $reservation->rooms->sum('total_price');
                $invoiceTotal = $invoice->hotelInvoiceRooms->sum('total_price');
                if ($reservationTotal != $invoiceTotal) {
                    $differences['total_amount'] = [
                        'reservation' => $reservationTotal,
                        'invoice' => $invoiceTotal,
                    ];
                }

                // ✅ Compare rooms one by one
                foreach ($reservation->rooms as $room) {
                    $invoiceRoom = $invoice->hotelInvoiceRooms->firstWhere('room_name', $room->name);

                    if (!$invoiceRoom) {
                        $differences['missing_room'][] = $room->name;
                        continue;
                    }

                    if ($room->total_room != $invoiceRoom->total_room) {
                        $differences['room_total_room'][$room->name] = [
                            'reservation' => $room->total_room,
                            'invoice' => $invoiceRoom->total_room,
                        ];
                    }

                    if ($room->total_price != $invoiceRoom->total_price) {
                        $differences['room_total_price'][$room->name] = [
                            'reservation' => $room->total_price,
                            'invoice' => $invoiceRoom->total_price,
                        ];
                    }

                    if ($room->currency_id != $invoiceRoom->currency_id) {
                        $differences['room_currency'][$room->name] = [
                            'reservation' => $room->currency_id,
                            'invoice' => $invoiceRoom->currency_id,
                        ];
                    }
                }

                // Return reservation only if differences found
                return !empty($differences);
            })
            ->values();
        return Inertia::render('UpdateHotelInvoice', [
            'reservations' => $reservations,

        ]);
//        return response()->json($reservations);
    }

    public function hotelInvoiceDownload(Request $request)
    {
        Log::info("invoice data",[
            'month' => $request->input('month'),
            'downloadType' => $request->input('downloadType'),
            'invoices' => $request->input('invoices'),
        ]);
        $requestedMonth = $request->input('month');
        $monthKey = Carbon::createFromFormat('F Y', $requestedMonth)->format('Y-m');
        $monthlyAdjustments = MonthlyHotelInvoiceAdjustment::where('month', $monthKey)->get();
        Log::info("Adjustment data",[
            'monthlyAdjustments' => $monthlyAdjustments
        ]);
        $today = now();
        $part1 = substr($today->format('Y'), 0, 2);
        $part2 = $today->format('d');
        $part3 = substr($today->format('Y'), 2, 2);
        $part4 = $today->format('m');
        $invoiceNo = $part3 . $part2 . $part1 . $part4;
        $formattedDate = $today->format('d F Y');
        $data = $request->only([
            'hotel',
            'hotelAddress',
            'commissionType',
            'expediaCollectsCommission',
            'hotelCollectsCommission',
            'month',
            'downloadType',
            'invoices',
        ]);
        $data['invoiceNo'] = $invoiceNo;
        $data['invoiceDate'] = $formattedDate;
        $data['monthlyAdjustments'] = $monthlyAdjustments;
        Log::info("from data variable",[
            'data' => $data
        ]);
        try {
            // 1. Render Blade view to HTML
            $html = View::make('pdf.invoiceCopy', $data)->render();

            // 2. Configure custom font (Nunito)
            $defaultConfig = (new ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];

            $defaultFontConfig = (new FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];

            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'margin_bottom' => 10,
                'fontDir' => array_merge($fontDirs, [
                    resource_path('fonts/Nunito'),
                ]),
                'fontdata' => $fontData + [
                        'nunito' => [
                            'R' => 'NunitoSans-Regular.ttf',
                            '600' => 'NunitoSans-SemiBold.ttf',
                            // Add 'I' => 'Nunito-Italic.ttf' if needed
                        ],
                        'nunito600' => [
                            'R' => 'NunitoSans-SemiBold.ttf',
                        ],
                        'nunitoR400' => [
                            'R' => 'NunitoSans-Regular.ttf',
                        ],
                    ],
                'default_font' => 'nunito'
            ]);

            // 3. Write HTML to PDF
            $mpdf->WriteHTML($html);
//            $guestName = $data['guest_name'] && $data['payment_method'] ?? 'guest';
//            $cleanGuestName = preg_replace('/[^A-Za-z0-9 _-]/', '_', $guestName);
//            $cleanGuestName = str_replace(' ', '_', $cleanGuestName);
//
//            $fileName = $cleanGuestName . ' .pdf';

            return response($mpdf->Output('', 'S'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="Invoice Data"',
            ]);


        } catch (Exception $e) {
            Log::error('PDF generation failed', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'PDF generation failed'], 500);
        }
    }

    //    Hotel Invoice Adjustment
    function getInvoiceAdjustments(Request $request)
    {
        $adjustments = MonthlyHotelInvoiceAdjustment::oldest()->get();
        $sources = Source::get();
        return Inertia::render('MonthlyAdjustment', [
            'adjustments' => $adjustments,
            'sources' => $sources
        ]);
    }
    function addAdjustment(Request $request)
    {
        $data = $request->validate([
            'month' => 'required|string|max:50|min:3',
            'purpose' => 'required|string|max:100|min:3',
            'type' => 'required|string|max:50|min:3',
            'source' => 'required|string|max:50|min:3',
            'amount' => 'required|string|max:50|min:3',
        ]);
        Log::info("data", $data);
        DB::beginTransaction();
        try {
            MonthlyHotelInvoiceAdjustment::create($data);
            DB::commit();

            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    function updateAdjustment(Request $request, $id)
    {

        $data = $request->validate([
            'month' => 'required|string|max:50|min:3',
            'purpose' => 'required|string|max:100|min:3',
            'type' => 'required|string|max:50|min:3',
            'source' => 'required|string|max:50|min:3',
            'amount' => 'required|string|max:50|min:3',
        ]);

        DB::beginTransaction();
        try {
            MonthlyHotelInvoiceAdjustment::where('id', $id)->update($data);
            DB::commit();
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            return Redirect::back()->withErrors($e->getMessage());
        }

    }
    function deleteAdjustment(Request $request)
    {
        $id = $request->id;
        try{
            $deleted =  MonthlyHotelInvoiceAdjustment::where('id', $id)->delete();
            $error = "";
            if(!$deleted){
                $error = "Adjustment not found or could not be deleted";
            }
            $data = ['message' => 'Adjustment Deleted Successfully', 'status' => true, 'error' => $error];
            return redirect()->route('dashboard/hotel-invoice/invoice-adjustment')->with($data );
        }
        catch(Exception $e){
            return Redirect::back()->withErrors($e->getMessage());
        }
    }








}
