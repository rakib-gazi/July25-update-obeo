<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HotelInvoiceController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\TokenVerificationMiddleware;
use Illuminate\Support\Facades\Route;


Route::get('/', [DashboardController::class, 'homePage'])->name('homePage');

//login
Route::post('/login', [UserController::class, 'login'])->name('login');
//logout
Route::get('/logout', [UserController::class,'logout'])->name('logout');



//Route::get('/phpinfo', function () {
//    phpinfo();
//});
Route::get('/reservationcopy', [HotelInvoiceController::class, 'hotelInvoicePreview1']);



Route::middleware([TokenVerificationMiddleware::class])
    ->group(function () {
        //user route
        Route::get('/dashboard',[DashboardController::Class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard/users',[UserController::class,'getUsers'])->name('getUsers');
        Route::post('/dashboard/users',[UserController::class,'addUser'])->name('addUser');
        Route::put('/dashboard/users/{id}',[UserController::class,'updateUser'])->name('updateUser');
        Route::get('/dashboard/delete-user/{id}', [UserController::class,'deleteUser'])->name('deleteUser');

        // settings route
        Route::get('/dashboard/reservations',[ReservationController::class, 'reservations'])->name('reservations');
        Route::get('/dashboard/copy',[ReservationController::class, 'reservationCopy'])->name('reservationCopy');
        // reservation
        Route::get('/dashboard/add-reservation',[ReservationController::class, 'reservation'])->name('reservation');
        Route::get('/dashboard/reservations/all-reservations',[ReservationController::class, 'getAllReservations'])->name('getAllReservations');
        Route::get('/dashboard/reservations/today-added-reservations',[ReservationController::class, 'getTodayAddedReservations'])->name('getTodayAddedReservations');
        Route::get('/dashboard/reservations/current-month-reservations',[ReservationController::class, 'getCurrentMonthReservations'])->name('getCurrentMonthReservations');
        Route::get('/dashboard/reservations/previous-month-reservations',[ReservationController::class, 'getPreviousMonthReservations'])->name('getPreviousMonthReservations');
        Route::get('/dashboard/reservations/next-month-reservations',[ReservationController::class, 'getNextMonthReservations'])->name('getNextMonthReservations');
        Route::post('/dashboard/add-reservation',[ReservationController::class,'addreservation'])->name('add-reservation');
        Route::put('/dashboard/update-reservation/{id}',[ReservationController::class,'updateReservation'])->name('updateReservation');
        Route::patch('/dashboard/update-status/{id}',[ReservationController::class,'updateStatus'])->name('updateStatus');
        Route::get('/dashboard/delete-reservation/{id}', [ReservationController::class,'deleteReservation'])->name('deleteReservation');

        // settings route
        Route::get('/dashboard/settings',[SettingsController::class, 'settings'])->name('settings');

        // Hotel Settings
        Route::get('/dashboard/settings/hotel-settings',[SettingsController::class, 'getHotels'])->name('getHotels');
        Route::post('/dashboard/settings/add-hotel',[SettingsController::class,'addHotel'])->name('addHotel');
        Route::put('/dashboard/settings/update-hotel/{id}',[SettingsController::class,'updateHotel'])->name('updateHotel');
        Route::get('/dashboard/settings/delete-hotel/{id}', [SettingsController::class,'deleteHotel'])->name('deleteHotel');

        //Currency Settings
        Route::get('dashboard/settings/currency-settings',[SettingsController::class, 'getCurrencies'])->name('getCurrencies');
        Route::post('/dashboard/settings/add-currency',[SettingsController::class,'addCurrency'])->name('addCurrency');
        Route::put('/dashboard/settings/update-currency/{id}',[SettingsController::class,'updateCurrency'])->name('updateCurrency');
        Route::get('/dashboard/settings/delete-currency/{id}', [SettingsController::class,'deleteCurrency'])->name('deleteCurrency');

        //Exchange Rate Settings
        Route::get('dashboard/settings/exchange-rate-settings',[SettingsController::class, 'getRates'])->name('getExchangeRates');
        Route::post('/dashboard/settings/add-rate',[SettingsController::class,'addRate'])->name('addRate');
        Route::put('/dashboard/settings/update-rate/{id}',[SettingsController::class,'updateRate'])->name('updateRate');
        Route::get('/dashboard/settings/delete-rate/{id}', [SettingsController::class,'deleteRate'])->name('deleteRate');

        //Source  Settings
        Route::get('dashboard/settings/source-settings',[SettingsController::class, 'getSource'])->name('getSource');
        Route::post('/dashboard/settings/add-source',[SettingsController::class,'addSource'])->name('addSource');
        Route::put('/dashboard/settings/update-source/{id}',[SettingsController::class,'updateSource'])->name('updateSource');
        Route::get('/dashboard/settings/delete-source/{id}', [SettingsController::class,'deleteSource'])->name('deleteSource');

        //Payment Method  Settings
        Route::get('dashboard/settings/payment-method-settings',[SettingsController::class, 'getPaymentMethod'])->name('getPaymentMethod');
        Route::post('/dashboard/settings/add-payment-method',[SettingsController::class,'addPaymentMethod'])->name('addPaymentMethod');
        Route::put('/dashboard/settings/update-payment-method/{id}',[SettingsController::class,'updatePaymentMethod'])->name('updatePaymentMethod');
        Route::get('/dashboard/settings/delete-payment-method/{id}', [SettingsController::class,'deletePaymentMethod'])->name('deletePaymentMethod');


        //reservation Status  Settings
        Route::get('dashboard/settings/reservation-status-settings',[SettingsController::class, 'getReservationStatus'])->name('getReservationStatus');
        Route::post('/dashboard/settings/add-reservation-status',[SettingsController::class,'addReservationStatus'])->name('addReservationStatus');
        Route::put('/dashboard/settings/update-reservation-status/{id}',[SettingsController::class,'updateReservationStatus'])->name('updateReservationStatus');
        Route::get('/dashboard/settings/delete-reservation-status/{id}', [SettingsController::class,'deleteReservationStatus'])->name('deleteReservationStatus');


        // Hotel invoice Route
        Route::get('/dashboard/hotel-invoice',[HotelInvoiceController::class, 'hotelInvoice'])->name('hotelInvoiceMainPage');
        Route::get('/dashboard/hotel-invoice/create-invoice',[ReservationController::class, 'getInvoiceEligibleReservations'])->name('getInvoiceEligibleReservations');
        Route::post('/dashboard/hotel-invoice/create-invoice',[HotelInvoiceController::class,'createInvoice'])->name('createInvoice');
        Route::get('/dashboard/hotel-invoice/all-invoices',[HotelInvoiceController::class,'getAllHotelInvoices'])->name('getAllHotelInvoices');
        Route::get('/dashboard/hotel-invoice/all-invoices/{id}',[HotelInvoiceController::class,'getHotelInvoicesByHotel'])->name('getHotelInvoicesByHotel');
        Route::get('/dashboard/hotel-invoice/eligible-invoices-for-update',[HotelInvoiceController::class,'getInvoiceEligibleForUpdate'])->name('getInvoiceEligibleForUpdate');
        Route::delete('/dashboard/hotel-invoice/delete/{id}', [HotelInvoiceController::class,'deleteInvoice'])->name('deleteInvoice');

        // pdf download route
        Route::post('/reservation/pdf', [ReservationController::class, 'download'])->name('reservation.pdf');
        Route::post('/hotel-invoice/pdf', [HotelInvoiceController::class, 'hotelInvoiceDownload'])->name('hotelInvoice.pdf');

        //monthly hotel invoice adjustments  route
        Route::get('/dashboard/hotel-invoice/invoice-adjustment',[HotelInvoiceController::class, 'getInvoiceAdjustments'])->name('getInvoiceAdjustments');
        Route::post('/dashboard/hotel-invoice/invoice-adjustment',[HotelInvoiceController::class,'addAdjustment'])->name('addAdjustment');
        Route::put('/dashboard/hotel-invoice/invoice-adjustment/{id}',[HotelInvoiceController::class,'updateAdjustment'])->name('updateAdjustment');
        Route::get('/dashboard/hotel-invoice/invoice-adjustment/{id}', [HotelInvoiceController::class,'deleteAdjustment'])->name('deleteAdjustment');



    });
