<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Currency;
use App\Models\Hotel;
use App\Models\PaymentMethod;
use App\Models\Rate;
use App\Models\Reservation;
use App\Models\ReservationChild;
use App\Models\ReservationRoom;
use App\Models\ReservationStatus;
use App\Models\Room;
use App\Models\Source;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;
use Mpdf\Mpdf;
use Spatie\Browsershot\Browsershot;

class ReservationController extends Controller
{
    //all reservation page
    function reservations(Request $request)
    {
        return Inertia::render('Reservations');
    }
    function reservationCopy(Request $request)
    {
        return Inertia::render('Copy');
    }
    //Reservation form
    function reservation(Request $request)
    {
        $hotels =  Hotel::select('id', 'hotelName')->get();
        $rates = Rate::select('id', 'rate')->get();
        $currencies = Currency::select('id', 'currency')->get();
        $sources = Source::select('id','source')->get();
        $payments = PaymentMethod::select('id', 'payment')->get();
        return Inertia::render('AddReservation',[
            'hotels' => $hotels,
            'rates' => $rates,
            'currencies' => $currencies,
            'sources' => $sources,
            'payments' => $payments
        ]);
    }
    //add reservation
    function addreservation(Request $request)
    {
        $messages = [
        '   reservation_no.regex' => 'Reservation number must be a number.',
            'hotel_id.required' =>'The hotel is required',
            'rate_id.required' =>'The Exchange Rate is required',
            'source_id.required' =>'The Reservation Source is required',
            'payment_method_id.required' =>'The Payment Method is required',
            'currency_id.required_with' => 'Advance currency is required',
            'phone.required_without' => 'Phone or email is required.',
            'email.required_without' => 'Phone or email is required.',
            'total_adult.integer' => 'Total adults must be a number.',

            'rooms.*.name.required' => 'Room name is required.',
            'rooms.*.total_night.required' => 'Night required.',
            'rooms.*.total_night.integer' => 'Must be number.',
            'rooms.*.total_night.min' => 'Minimum 1 night.',
            'rooms.*.total_night.max' => 'Maximum 99 night.',
            'rooms.*.total_room.required' => 'Rooms required.',
            'rooms.*.total_room.integer' => 'Must be number',
            'rooms.*.total_room.min' => 'Minimum 1 Room.',
            'rooms.*.total_room.max' => 'Maximum 50 Room.',
            'rooms.*.total_price.required' => 'Price required.',
            'rooms.*.total_price.numeric' => 'Must be number',
            'rooms.*.total_price.max' => 'Maximum 0 .',
            'rooms.*.currency_id.required' => 'Currency required.',
        ];
        $data = $request->validate([
            'user_id' => 'required',
            'status_id' => 'nullable|exists:reservation_statuses,id',
            'reservation_no' => 'required|regex:/^\d+$/|unique:reservations,reservation_no',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after_or_equal:check_in',
            'reservation_date' => 'required|date',
            'hotel_id' => 'required|exists:hotels,id',
            'guest_name' => 'required|string|max:255',
            'rate_id' => 'required|exists:rates,id',
            'total_advance' => 'nullable|numeric|min:0',
            'currency_id' => 'nullable|required_with:total_advance|exists:currencies,id',
            'source_id' => 'required|exists:sources,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'phone' => 'required_without:email|nullable|string|max:17|min:11',
            'email' => 'required_without:phone|nullable|email',
            'total_adult' => 'required|integer|min:1',
            'request' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:255',

            'children' => 'nullable|array',
            'children.*.age' => 'required|integer|min:0|max:17',

            'rooms' => 'required|array|min:1',

            'rooms.*.name' => 'required|string|max:255',
            'rooms.*.total_night' => 'required|integer|min:1|max:99',
            'rooms.*.total_room' => 'required|integer|min:1||max:99',
            'rooms.*.total_price' => 'required|numeric|min:0',
            'rooms.*.currency_id' => 'required|exists:currencies,id',
        ],$messages);


        DB::beginTransaction();
        try{
            do {
                // Numeric-only: YYYYMMDD + 4-digit random number
                $prefix = now()->format('Ymd'); // e.g. 20250527
                $random = str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT); // e.g. 0384
                $obeoSL = (int)($prefix . $random); // e.g. 202505270384
            } while (Reservation::where('obeo_sl', $obeoSL)->exists());

            $reservation = Reservation::create([
                'user_id' => (int) $data['user_id'] ?? null,
                'obeo_sl' => $obeoSL,
                'status_id' => $data['status_id'] ?? null,
                'reservation_no' => $data['reservation_no'],
                'check_in' => $data['check_in'],
                'check_out' => $data['check_out'],
                'reservation_date' => $data['reservation_date'],
                'hotel_id' => $data['hotel_id'],
                'guest_name' => $data['guest_name'],
                'rate_id' => $data['rate_id'],
                'total_advance' => $data['total_advance'],
                'currency_id' => $data['currency_id'] ?? null,
                'source_id' => $data['source_id'],
                'payment_method_id' => $data['payment_method_id'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'total_adult' => $data['total_adult'],
                'request' => $data['request'],
                'comment' => $data['comment'],
            ]);
            // Save rooms
            foreach ($data['rooms'] as $roomData) {
                $room = Room::create([
                    'name' => $roomData['name'],
                    'total_room' => $roomData['total_room'],
                    'total_night' => $roomData['total_night'],
                    'total_price' => $roomData['total_price'],
                    'currency_id' => $roomData['currency_id'],
                ]);

                ReservationRoom::create([
                    'room_id' => $room->id,
                    'reservation_id' => $reservation->id,
                ]);
            }

            // Save children (if any)
            if (!empty($data['children'])) {
                foreach ($data['children'] as $childData) {
                    $child = Child::create(['age' => $childData['age']]);

                    ReservationChild::create([
                        'child_id' => $child->id,
                        'reservation_id' => $reservation->id,
                    ]);
                }
            }
            DB::commit();
            return redirect()->back();
        }
        catch (Exception $exception){
            DB::rollBack();
            return back()->withErrors(['error' => $exception->getMessage()]);
        }
    }
    // all reservations
    function getAllReservations()
    {
        $hotels =  Hotel::select('id', 'hotelName')->get();
        $rates = Rate::select('id', 'rate')->get();
        $currencies = Currency::select('id', 'currency')->get();
        $sources = Source::select('id','source')->get();
        $payments = PaymentMethod::select('id', 'payment')->get();
        $status = ReservationStatus::select('id', 'status')->get();
        $reservations = Reservation::with([
            'user:id,fullName',
            'reservation_status:id,status',
            'hotel:id,hotelName',
            'rate:id,rate',
            'currency:id,currency',
            'source:id,source',
            'paymentMethod:id,payment',
            'children:id,age',
            'rooms' => function ($query) {
                $query->select('rooms.id', 'name', 'total_room', 'total_night', 'total_price', 'currency_id')
                    ->with('currency:id,currency');
            }
        ])->get()
            ->makeHidden([
                'hotel_id', 'rate_id', 'currency_id', 'source_id', 'payment_method_id'
            ])
            ->map(function ($reservation) {
                // Remove pivot from children
                $reservation->children->transform(function ($child) {
                    unset($child->pivot);
                    return $child;
                });

                // Optionally remove pivot from rooms too
                $reservation->rooms->transform(function ($room) {
                    unset($room->pivot);
                    return $room;
                });

                return $reservation;
            });

        return Inertia::render('AllReservations', [
            'reservations' => $reservations,
            'hotels' => $hotels,
            'rates' => $rates,
            'currencies' => $currencies,
            'sources' => $sources,
            'payments' => $payments,
            'status' => $status
        ]);
//        return response()->json($reservations);
    }
    // only today added reservation
    function getTodayAddedReservations()
    {
        $hotels =  Hotel::select('id', 'hotelName')->get();
        $rates = Rate::select('id', 'rate')->get();
        $currencies = Currency::select('id', 'currency')->get();
        $sources = Source::select('id','source')->get();
        $payments = PaymentMethod::select('id', 'payment')->get();
        $reservations = Reservation::whereDate('created_at', Carbon::today())->with([
            'user:id,fullName',
            'reservation_status:id,status',
            'hotel:id,hotelName',
            'rate:id,rate',
            'currency:id,currency',
            'source:id,source',
            'paymentMethod:id,payment',
            'children:id,age',
            'rooms' => function ($query) {
                $query->select('rooms.id', 'name', 'total_room', 'total_night', 'total_price', 'currency_id')
                    ->with('currency:id,currency');
            }
        ])->get()
            ->makeHidden([
                'hotel_id', 'rate_id', 'currency_id', 'source_id', 'payment_method_id'
            ])
            ->map(function ($reservation) {
                // Remove pivot from children
                $reservation->children->transform(function ($child) {
                    unset($child->pivot);
                    return $child;
                });

                // Optionally remove pivot from rooms too
                $reservation->rooms->transform(function ($room) {
                    unset($room->pivot);
                    return $room;
                });

                return $reservation;
            });

        return Inertia::render('TodayAddedReservations', [
            'reservations' => $reservations,
            'hotels' => $hotels,
            'rates' => $rates,
            'currencies' => $currencies,
            'sources' => $sources,
            'payments' => $payments
        ]);
//        return response()->json($reservations);
    }
    // update reservation
    function updateReservation(Request $request, $id)
    {
        $messages = [
            'reservation_no.regex' => 'Reservation number must be a number.',
            'hotel_id.required' =>'The hotel is required',
            'rate_id.required' =>'The Exchange Rate is required',
            'source_id.required' =>'The Reservation Source is required',
            'payment_method_id.required' =>'The Payment Method is required',
            'total_advance.required_with' => 'Total advance is required.',
            'currency_id.required_with' => 'Currency is required.',
            'phone.required_without' => 'Phone or email is required.',
            'email.required_without' => 'Phone or email is required.',
            'total_adult.integer' => 'Total adults must be a number.',
            'rooms.*.name.required' => 'Room name is required.',
            'rooms.*.total_night.required' => 'Night required.',
            'rooms.*.total_night.integer' => 'Must be number.',
            'rooms.*.total_night.min' => 'Minimum 1 night.',
            'rooms.*.total_night.max' => 'Maximum 99 night.',
            'rooms.*.total_room.required' => 'Rooms required.',
            'rooms.*.total_room.integer' => 'Must be number',
            'rooms.*.total_room.min' => 'Minimum 1 Room.',
            'rooms.*.tota l_room.max' => 'Maximum 50 Room.',
            'rooms.*.total_price.required' => 'Price required.',
            'rooms.*.total_price.numeric' => 'Must be number',
            'rooms.*.total_price.max' => 'Maximum 0 .',
            'rooms.*.currency_id.required' => 'Currency required.',
        ];

        $data = $request->validate([
            'user_id' => 'required',
            'status_id' => 'nullable|exists:reservation_statuses,id',
            'reservation_no' => 'required|regex:/^\d+$/|unique:reservations,reservation_no,' . $id,
            'check_in' => 'required|date',
            'check_out' => 'required|date|after_or_equal:check_in',
            'reservation_date' => 'required|date',
            'hotel_id' => 'required|exists:hotels,id',
            'guest_name' => 'required|string|max:255',
            'rate_id' => 'required|exists:rates,id',
            'total_advance' => 'nullable|required_with:currency_id|numeric|min:0',
            'currency_id' => 'nullable|required_with:total_advance|exists:currencies,id',
            'source_id' => 'required|exists:sources,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'phone' => 'required_without:email|nullable|string|max:17|min:11',
            'email' => 'required_without:phone|nullable|email',
            'total_adult' => 'required|integer|min:1',
            'request' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:255',

            'children' => 'nullable|array',
            'children.*.age' => 'required|integer|min:0|max:17',

            'rooms' => 'required|array|min:1',
            'rooms.*.id' => 'nullable|integer|exists:rooms,id',
            'rooms.*.name' => 'required|string|max:255',
            'rooms.*.total_night' => 'required|integer|min:1|max:99',
            'rooms.*.total_room' => 'required|integer|min:1||max:99',
            'rooms.*.total_price' => 'required|numeric|min:0',
            'rooms.*.currency_id' => 'required|exists:currencies,id',
        ], $messages);
        DB::beginTransaction();
        try {
            $reservation = Reservation::findOrFail($id);

            $reservation->update([
                'user_id' => $data['user_id'] ?? null,
                'status_id' => $data['status_id'] ?? null,
                'reservation_no' => $data['reservation_no'],
                'check_in' => $data['check_in'],
                'check_out' => $data['check_out'],
                'reservation_date' => $data['reservation_date'],
                'hotel_id' => $data['hotel_id'],
                'guest_name' => $data['guest_name'],
                'rate_id' => $data['rate_id'],
                'total_advance' => $data['total_advance'],
                'currency_id' => $data['currency_id'] ?? null,
                'source_id' => $data['source_id'],
                'payment_method_id' => $data['payment_method_id'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'total_adult' => $data['total_adult'],
                'request' => $data['request'],
                'comment' => $data['comment'],
            ]);


            // Handle children
            $currentChildIds = $reservation->children()->pluck('children.id')->toArray();
            $updatedChildIds = [];

            if (!empty($data['children'])) {
                foreach ($data['children'] as $childData) {
                    if (!empty($childData['id'])) {
                        // Update existing child
                        $child = Child::find($childData['id']);
                        if ($child) {
                            $child->update(['age' => $childData['age']]);
                            $updatedChildIds[] = $child->id;

                            // Make sure the pivot exists
                            ReservationChild::updateOrCreate([
                                'reservation_id' => $reservation->id,
                                'child_id' => $child->id,
                            ]);
                        }
                    } else {
                        // New child
                        $child = Child::create(['age' => $childData['age']]);
                        ReservationChild::create([
                            'reservation_id' => $reservation->id,
                            'child_id' => $child->id,
                        ]);
                        $updatedChildIds[] = $child->id;
                    }
                }
            }

            // Delete removed children (both pivot & child)
            $childIdsToDelete = array_diff($currentChildIds, $updatedChildIds);

            if (!empty($childIdsToDelete)) {
                // Delete pivot records
                ReservationChild::where('reservation_id', $reservation->id)
                    ->whereIn('child_id', $childIdsToDelete)
                    ->delete();

                // Delete the child records
                Child::whereIn('id', $childIdsToDelete)->delete();
            }




            $currentRoomIds = $reservation->rooms()->pluck('rooms.id')->toArray();
            $updatedRoomIds = [];
            foreach ($data['rooms'] as $roomData) {
                if (!empty($roomData['id'])) {
                    // Update existing room
                    $room = Room::find($roomData['id']);
                    $room->update([
                        'name' => $roomData['name'],
                        'total_room' => $roomData['total_room'],
                        'total_night' => $roomData['total_night'],
                        'total_price' => $roomData['total_price'],
                        'currency_id' => $roomData['currency_id'],
                    ]);
                } else {
                    // Create new room
                    $room = Room::create([
                        'name' => $roomData['name'],
                        'total_room' => $roomData['total_room'],
                        'total_night' => $roomData['total_night'],
                        'total_price' => $roomData['total_price'],
                        'currency_id' => $roomData['currency_id'],
                    ]);
                }

                $updatedRoomIds[] = $room->id;
                ReservationRoom::updateOrCreate(
                    [
                        'reservation_id' => $reservation->id,
                        'room_id' => $room->id,
                    ]
                );
            }
            $roomIdsToDelete = array_diff($currentRoomIds, $updatedRoomIds);

            if (!empty($roomIdsToDelete)) {
                // First remove from pivot table
                ReservationRoom::where('reservation_id', $reservation->id)
                    ->whereIn('room_id', $roomIdsToDelete)
                    ->delete();

                // Then delete from rooms table
                Room::whereIn('id', $roomIdsToDelete)->delete();
            }
            DB::commit();
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function deleteReservation(Request $request)
    {
        $id = $request->id;

        DB::beginTransaction();

        try {
            $reservation = Reservation::find($id);

            if (!$reservation) {
                return redirect()->back()->withErrors('Reservation not found.');
            }

            // Get related room and child IDs
            $roomIds = $reservation->rooms()->pluck('rooms.id')->toArray();
            $childIds = $reservation->children()->pluck('children.id')->toArray();

            // Delete pivot table records first (no detach, direct delete)
            DB::table('reservation_rooms')->where('reservation_id', $id)->delete();
            DB::table('reservation_children')->where('reservation_id', $id)->delete();

            // Then delete related child and room records
            Room::whereIn('id', $roomIds)->delete();
            Child::whereIn('id', $childIds)->delete();

            // Finally delete the reservation itself
            $reservation->delete();

            DB::commit();

            return redirect()->back()->with([
                'message' => 'Reservation deleted successfully.',
                'status' => true,
                'error' => ''
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    function updateStatus(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'status_id' => 'required|exists:reservation_statuses,id',
            ]);
            $reservation = Reservation::findOrFail($id);
            $reservation->update([
                'status_id' => $request->status_id,
            ]);
            DB::commit();
            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

//    public function download(Request $request)
//    {
//        Log::info('PDF download request received', $request->all());
//
//        $data = $request->only([
//            'obeo_sl',
//            'reservation_no',
//            'guest_name',
//            'check_in',
//            'check_out',
//            'reservation_date',
//            'hotelName',
//            'email',
//            'phone',
//            'request',
//            'comment',
//            'rooms',
//            'total_adult',
//            'children',
//            'total_advance',
//            'rate',
//            'currency',
//            'payment_method',
//            'source',
//            'total_night',
//            'totalUsd',
//            'totalBdt',
//            'totalPayInHotel'
//        ]);
//
//        try {
//            $html = view('pdf.reservationCopy', $data)->render();
//
//            $path = storage_path('app/public/reservation.pdf');
//
//            Browsershot::html($html)
//                ->format('A4')
//                ->showBackground()
//                ->margins(8, 6, 10, 6)
//                ->save($path);
//            return response()->download($path)->deleteFileAfterSend(true);
//        } catch (Exception $e) {
//            Log::error('PDF generation failed', ['message' => $e->getMessage()]);
//            return response()->json(['error' => 'PDF generation failed'], 500);
//        }
//    }


//    public function download(Request $request)
//    {
//        Log::info('PDF download request received', $request->all());
//
//        $data = $request->only([
//            'obeo_sl',
//            'reservation_no',
//            'guest_name',
//            'check_in',
//            'check_out',
//            'reservation_date',
//            'hotelName',
//            'email',
//            'phone',
//            'request',
//            'comment',
//            'rooms',
//            'total_adult',
//            'children',
//            'total_advance',
//            'rate',
//            'currency',
//            'payment_method',
//            'source',
//            'total_night',
//            'totalUsd',
//            'totalBdt',
//            'totalPayInHotel'
//        ]);
//
//        try {
//            // Generate the PDF using dompdf
//            $pdf = Pdf::loadView('pdf.reservationCopy', $data)
//                ->setPaper('A4');
//
//            $path = storage_path('app/public/reservation.pdf');
//
//            // Save the PDF file
//            $pdf->save($path);
//
//            // Return download response
//            return response()->download($path)->deleteFileAfterSend(true);
//
//        } catch (Exception $e) {
//            Log::error('PDF generation failed', ['message' => $e->getMessage()]);
//            return response()->json(['error' => 'PDF generation failed'], 500);
//        }
//    }

    public function download(Request $request)
    {
        Log::info('PDF download request received', $request->all());

        $data = $request->only([
            'obeo_sl',
            'reservation_no',
            'guest_name',
            'check_in',
            'check_out',
            'reservation_date',
            'hotelName',
            'email',
            'phone',
            'request',
            'comment',
            'rooms',
            'total_adult',
            'children',
            'total_advance',
            'rate',
            'currency',
            'payment_method',
            'source',
            'total_night',
            'totalUsd',
            'totalBdt',
            'totalPayInHotel'
        ]);

        try {
            // Render Blade to HTML
            $html = View::make('pdf.reservationCopy', $data)->render();

            // Create mPDF instance
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'margin_bottom' => 10,
            ]);

            // Write HTML to PDF
            $mpdf->WriteHTML($html);

            // Generate file name
            $fileName = 'reservation_' . ($data['reservation_no'] ?? 'file') . '.pdf';

            // Output to browser
            return response($mpdf->Output($fileName, 'S'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
        } catch (\Exception $e) {
            Log::error('PDF generation failed', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'PDF generation failed'], 500);
        }
    }
}
