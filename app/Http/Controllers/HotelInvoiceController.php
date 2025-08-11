<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
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
        $hotel_id = $data['hotel_id'];
//        inv_no inv_date
        $total_amount = $data['total_amount'];
        $total_advance = $data['total_advance'];
        $currency_id = $data['advanceCurency'];
//        $hotel_invoice_id = $data['hotel_invoice_id'];
        $reservation_id= $data['reservation_id'];
        Log::info('Invoice information', [
//            'hotel_invoice_id' => $hotel_invoice_id,
            'reservation_id'   => $reservation_id,
            'currency_id'      => $currency_id,
            'total_amount'     => $total_amount,
            'total_advance'    => $total_advance,
            'hotel_id'         => $hotel_id,
        ]);


//        $data = $request->validate([
//            'payment' => 'required|integer',
//        ]);
//        DB::beginTransaction();
//        try {
//            PaymentMethod::create($data);
//            DB::commit();
//
//            return redirect()->back();
//        } catch (Exception $e) {
//            DB::rollBack();
//            return back()->withErrors(['error' => $e->getMessage()]);
//        }
    }
}
