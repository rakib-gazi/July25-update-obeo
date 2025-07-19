<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Hotel;
use App\Models\PaymentMethod;
use App\Models\Rate;
use App\Models\ReservationStatus;
use App\Models\Source;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class SettingsController extends Controller
{
    //

    function settings(Request $request)
    {
        return Inertia::render('Settings');
    }

//    Hotel Settings

    function getHotels()
    {
        $hotels = Hotel::oldest()->get();

        return Inertia::render('HotelSettings', [
            'hotels' => $hotels
        ]);
    }
    function addHotel(Request $request)
    {
        $data = $request->validate([
            'hotelName' => 'required|string|max:100|min:3|unique:hotels,hotelName',
            'hotelAddress' => 'required|string|max:300|min:3',
            'commissionType' => 'required',
            'expediaCollectsCommission' => 'nullable|numeric|between:0,100',
            'hotelCollectsCommission' => 'nullable|numeric|between:0,100',
        ]);
        DB::beginTransaction();
        try {
             Hotel::create($data);
            DB::commit();

            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    function updateHotel(Request $request, $id)
    {

        $data = $request->validate([
            'hotelName' => 'required|string|max:100|min:3|unique:hotels,hotelName,' . $id,
            'hotelAddress' => 'required|string|max:300|min:3',
            'commissionType' => 'required',
            'expediaCollectsCommission' => 'nullable|numeric|between:0,100',
            'hotelCollectsCommission' => 'nullable|numeric|between:0,100',
        ]);

        DB::beginTransaction();
        try {
            Hotel::where('id', $id)->update($data);
            DB::commit();
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            return Redirect::back()->withErrors($e->getMessage());
        }

    }
    function deleteHotel(Request $request)
    {
        $id = $request->id;
        try{
            $deleted =  Hotel::where('id', $id)->delete();
            $error = "";
            if(!$deleted){
                $error = "Hotel not found or could not be deleted";
            }
            $data = ['message' => 'Hotel Deleted Successfully', 'status' => true, 'error' => $error];
            return redirect()->route('settings/hotel-settings')->with($data );
        }
        catch(Exception $e){
            return Redirect::back()->withErrors($e->getMessage());
        }
    }




//    Currency Settings

    function getCurrencies()
    {
        $currencies = Currency::oldest()->get();

        return Inertia::render('CurrencySettings', [
            'currencies' => $currencies
        ]);
    }
    function addCurrency(Request $request)
    {
        $data = $request->validate([
            'currency' => 'required|string|max:5|min:3|unique:currencies,currency',
        ]);
        DB::beginTransaction();
        try {
            Currency::create($data);
            DB::commit();

            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    function updateCurrency(Request $request, $id)
    {

        $data = $request->validate([
            'currency' => 'required|string|max:100|min:3|unique:currencies,currency,' . $id,
        ]);

        DB::beginTransaction();
        try {
            Currency::where('id', $id)->update($data);
            DB::commit();
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            return Redirect::back()->withErrors($e->getMessage());
        }

    }
    function deleteCurrency(Request $request)
    {
        $id = $request->id;
        try{
            $deleted =  Currency::where('id', $id)->delete();
            $error = "";
            if(!$deleted){
                $error = "Currency not found or could not be deleted";
            }
            $data = ['message' => 'Currency Deleted Successfully', 'status' => true, 'error' => $error];
            return redirect()->route('settings/currency-settings')->with($data );
        }
        catch(Exception $e){
            return Redirect::back()->withErrors($e->getMessage());
        }
    }



//    Exchange Rate Settings
    function getRates(Request $request)
    {
        $rates = Rate::oldest()->get();

        return Inertia::render('ExchangeRateSettings', [
            'rates' => $rates
        ]);
    }
    function addRate(Request $request)
    {
        $data = $request->validate([
            'rate' => 'required|numeric|between:80,200|unique:rates,rate',
        ]);
        DB::beginTransaction();
        try {
            Rate::create($data);
            DB::commit();

            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    function updateRate(Request $request, $id)
    {

        $data = $request->validate([
            'rate' => 'required|numeric|between:80,200|unique:rates,rate,' . $id,
        ]);

        DB::beginTransaction();
        try {
            Rate::where('id', $id)->update($data);
            DB::commit();
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            return Redirect::back()->withErrors($e->getMessage());
        }

    }
    function deleteRate(Request $request)
    {
        $id = $request->id;
        try{
            $deleted =  Rate::where('id', $id)->delete();
            $error = "";
            if(!$deleted){
                $error = "Exchange Rate not found or could not be deleted";
            }
            $data = ['message' => 'Exchange Rate Deleted Successfully', 'status' => true, 'error' => $error];
            return redirect()->route('settings/exchange-rate-settings')->with($data );
        }
        catch(Exception $e){
            return Redirect::back()->withErrors($e->getMessage());
        }
    }

    //    reservation Source Settings
    function getSource(Request $request)
    {
        $sources = Source::oldest()->get();

        return Inertia::render('SourceSettings', [
            'sources' => $sources
        ]);
    }
    function addSource(Request $request)
    {
        $data = $request->validate([
            'source' => 'required|string|max:50|min:3|unique:sources,source',
        ]);
        DB::beginTransaction();
        try {
            Source::create($data);
            DB::commit();

            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    function updateSource(Request $request, $id)
    {

        $data = $request->validate([
            'source' => 'required|string|max:50|min:3|unique:sources,source,' . $id,
        ]);

        DB::beginTransaction();
        try {
            Source::where('id', $id)->update($data);
            DB::commit();
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            return Redirect::back()->withErrors($e->getMessage());
        }

    }
    function deleteSource(Request $request)
    {
        $id = $request->id;
        try{
            $deleted =  Source::where('id', $id)->delete();
            $error = "";
            if(!$deleted){
                $error = "Reservation Source not found or could not be deleted";
            }
            $data = ['message' => 'Reservation Source Deleted Successfully', 'status' => true, 'error' => $error];
            return redirect()->route('settings/source-settings')->with($data );
        }
        catch(Exception $e){
            return Redirect::back()->withErrors($e->getMessage());
        }
    }


    //    Payment Method Settings
    function getPaymentMethod(Request $request)
    {
        $payments = PaymentMethod::oldest()->get();

        return Inertia::render('PaymentMethodSettings', [
            'payments' => $payments
        ]);
    }
    function addPaymentMethod(Request $request)
    {
        $data = $request->validate([
            'payment' => 'required|string|max:50|min:3|unique:payment_methods,payment',
        ]);
        DB::beginTransaction();
        try {
            PaymentMethod::create($data);
            DB::commit();

            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    function updatePaymentMethod(Request $request, $id)
    {

        $data = $request->validate([
            'payment' => 'required|string|max:50|min:3|unique:payment_methods,payment,' . $id,
        ]);

        DB::beginTransaction();
        try {
            PaymentMethod::where('id', $id)->update($data);
            DB::commit();
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            return Redirect::back()->withErrors($e->getMessage());
        }

    }
    function deletePaymentMethod(Request $request)
    {
        $id = $request->id;
        try{
            $deleted =  PaymentMethod::where('id', $id)->delete();
            $error = "";
            if(!$deleted){
                $error = "Payment Method not found or could not be deleted";
            }
            $data = ['message' => 'Payment Method Deleted Successfully', 'status' => true, 'error' => $error];
            return redirect()->route('settings/payment-method-settings')->with($data );
        }
        catch(Exception $e){
            return Redirect::back()->withErrors($e->getMessage());
        }
    }


    //    resrvation Status Settings
    function getReservationStatus(Request $request)
    {
        $statuses = ReservationStatus::oldest()->get();

        return Inertia::render('ReservationStatusSettings', [
            'statuses' => $statuses
        ]);
    }
    function addReservationStatus(Request $request)
    {
        $data = $request->validate([
            'status' => 'required|string|max:50|min:3|unique:reservation_statuses,status',
        ]);
        DB::beginTransaction();
        try {
            ReservationStatus::create($data);
            DB::commit();

            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    function updateReservationStatus(Request $request, $id)
    {

        $data = $request->validate([
            'status' => 'required|string|max:50|min:3|unique:reservation_statuses,status,' . $id,
        ]);

        DB::beginTransaction();
        try {
            ReservationStatus::where('id', $id)->update($data);
            DB::commit();
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            return Redirect::back()->withErrors($e->getMessage());
        }

    }
    function deleteReservationStatus(Request $request)
    {
        $id = $request->id;
        try{
            $deleted =  ReservationStatus::where('id', $id)->delete();
            $error = "";
            if(!$deleted){
                $error = "Reservation Status not found or could not be deleted";
            }
            $data = ['message' => 'Reservation Status Deleted Successfully', 'status' => true, 'error' => $error];
            return redirect()->route('settings/reservation-status-settings')->with($data );
        }
        catch(Exception $e){
            return Redirect::back()->withErrors($e->getMessage());
        }
    }

}
