@php
    $logoPath = public_path('images/obeologo.png');
    $bookingPath = public_path('images/booking.png');
    $expediaPath = public_path('images/expedia.png');
    $airbnbPath = public_path('images/airbnb.png');
    $cTripPath = public_path('images/ctrip.png');
    $makePath = public_path('images/make.png');

    $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';
    $bookingData = file_exists($bookingPath) ? base64_encode(file_get_contents($bookingPath)) : '';
    $expediaData = file_exists($expediaPath) ? base64_encode(file_get_contents($expediaPath)) : '';
    $airbnbData = file_exists($airbnbPath) ? base64_encode(file_get_contents($airbnbPath)) : '';
    $cTripData = file_exists($cTripPath) ? base64_encode(file_get_contents($cTripPath)) : '';
    $makeData = file_exists($makePath) ? base64_encode(file_get_contents($makePath)) : '';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{$guest_name}}</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-50 ">
<div class=" mx-auto p-4">
    <!-- Header -->
    <div class=" p-4 rounded-2xl shadow-md mb-4  flex justify-between items-center bg-white">
        <img src="data:image/png;base64,{{ $logoData }}" alt="Logo" class="h-16">
        <div class="text-right">
            <div class="flex justify-end">
                @if ($source === 'Booking.com')
                    <img src="data:image/png;base64,{{ $bookingData }}" alt="Reservation Source" class="h-6">
                @elseif ($source === 'Expedia')
                    <img src="data:image/png;base64,{{ $expediaData }}" alt="Reservation Source" class="h-8">
                @elseif ($source === 'Airbnb')
                    <img src="data:image/png;base64,{{ $airbnbData }}" alt="Reservation Source" class="h-8">
                @elseif ($source === 'Ctrip')
                    <img src="data:image/png;base64,{{ $cTripData }}" alt="Reservation Source" class="h-10">
                @elseif ($source === 'Makemytrip')
                    <img src="data:image/png;base64,{{ $makeData }}" alt="Reservation Source" class="h-10">
                @else
                    <h1 class="font-bold text-cyan-950 text-xl">Direct Booking</h1>
                @endif
            </div>
            <h1 class="text-xl font-bold text-cyan-950">Hotel Reservation</h1>
            <h3 class="text-lg font-lg text-cyan-950">{{$hotelName}}</h3>
        </div>
    </div>
    <!-- Reservation information -->
    <div class="rounded-2xl bg-white shadow-md mt-2">
        <h1 class="bg-[#9f825c21] py-1 text-black text-center rounded-t-2xl text-lg font-semibold ">Reservation Information</h1>
        <div class="p-4  grid grid-cols-3  gap-2">
            <div>
                <p class="text-xs font-semibold">Guest Name</p>
                <h2 class="text-md  mb-1 text-black">{{$guest_name}}</h2>
            </div>
            <div>
                <p class="text-xs font-semibold">Reservation No</p>
                <h2 class="text-md  mb-1 text-black">{{$reservation_no}}</h2>
            </div>
            <div>
                <p class="text-xs font-semibold">Obeo Sl</p>
                <h2 class="text-md  mb-1 text-black">{{$obeo_sl}}</h2>
            </div>
            <div>
                <p class="text-xs font-semibold">Check In</p>
                <h2 class="text-md  mb-1 text-black">{{$check_in}}</h2>
            </div>
            <div>
                <p class="text-xs font-semibold">Check Out</p>
                <h2 class="text-md  mb-1 text-black">{{$check_out}}</h2>
            </div>
            <div>
                <p class="text-xs font-semibold">Booked on</p>
                <h2 class="text-md  mb-1 text-black">{{$reservation_date}}</h2>
            </div>
            <div>
                <p class="text-xs font-semibold">Total Room</p>
                <h2 class="text-md  mb-1 text-black">{{ is_array($rooms) ? count($rooms) : 'N/A' }} Rooms</h2>
            </div>
            <div>
                <p class="text-xs font-semibold">Total Nights</p>
                <h2 class="text-md  mb-1 text-black">{{$total_night}} Nights</h2>
            </div>
            <div>
                <p class="text-xs font-semibold">Total Adults</p>
                <h2 class="text-md  mb-1 text-black">{{$total_adult}} Adults</h2>
            </div>
            <div>
                <p class="text-xs font-semibold">Childrens & Age ({{ count($children) }})</p>
                <h2 class="text-md  mb-1 text-black">
                    @if (is_array($children) && count($children) > 0)
                         {{ collect($children)->pluck('age')->implode(', ') }} years
                    @else
                         N/A
                    @endif
                </h2>
            </div>
            <div>
                <p class="text-xs font-semibold">Phone Number</p>
                <h2 class="text-md  mb-1 text-black">{{$phone ?? 'N/A'}}</h2>
            </div>
            <div>
                <p class="text-xs font-semibold">Email</p>
                <h2 class="text-md  mb-1 text-black">{{$email ?? 'N/A'}}</h2>
            </div>
        </div>
    </div>
    <!--payment & Pricing  -->
    <div class="rounded-2xl bg-white shadow-md mt-2">
        <h1 class="bg-[#9f825c21] py-1 text-black text-center rounded-t-2xl text-lg font-semibold ">Payment & Pricing </h1>
        <div class="p-4  grid grid-cols-3  gap-2">
            <div>
                <p class="text-xs font-semibold">Price (USD) </p>
                <h2 class="text-md  mb-1 text-black">${{$totalUsd}}</h2>
            </div>
            <div class="flex flex-col justify-end">
                <p class="text-xs font-semibold">Exchange Rate </p>
                <h2 class="text-md  mb-1 text-black">{{$rate}} TK</h2>
            </div>
            <div class="flex flex-col justify-end">
                <p class="text-xs font-semibold">Total Price (BDT) </p>
                <h2 class="text-md  mb-1 text-black">{{$totalBdt}} TK</h2>
            </div>
            <div class="flex flex-col justify-end">
                <p class="text-xs font-semibold">Total Advance </p>
                <h2 class="text-md  mb-1 text-black">{{$total_advance}} TK</h2>
            </div>
            <div class="flex flex-col justify-end">
                <p class="text-xs font-semibold">Total Pay In Hotel</p>
                <h2 class="text-md  mb-1 text-black">{{$totalPayInHotel}} TK</h2>
            </div>
            <div class="flex flex-col justify-end">
                <p class="text-xs font-semibold">Payment Method</p>
                <h2 class="text-md  mb-1 text-black">{{$payment_method}}</h2>
            </div>
        </div>
    </div>
    <!-- Room Wise Payment Details -->
    <div class="rounded-2xl bg-white shadow-md mt-2">
        <h1 class="bg-[#9f825c21] py-1 text-black text-center rounded-t-2xl text-lg font-semibold ">Room Wise Information & Price Details</h1>
        @foreach($rooms as $room)
            @php
                $roomName = $room['name'];
                $totalRoom = $room['total_room'];
                $totalNight = $room['total_night'];
                $totalPrice = (float)$room['total_price'];
                $rate = (float) $rate;
                $baseRateCount = $totalRoom * $totalNight;
                // currency is nested array too:
                $currency = $room['currency']['currency'];

                $totalPriceInBdt = $currency === 'USD' ?  $totalPrice * $rate : $totalPrice;
                $totalBasePriceForRoom = $totalPriceInBdt/$baseRateCount;
                $totalRoomPrice = $totalRoom * $totalBasePriceForRoom;
                $totalNightPrice= $totalRoomPrice * $totalNight;
            @endphp
            <div class="px-4 py-2">
                <div class="flex justify-between">
                    <h2 class="text-md font-medium mb-1 text-black">{{$roomName}} ({{$totalRoom}})</h2>
                    <h2 class="text-md font-medium mb-1 text-black"><span class="text-xs">({{$totalRoom}} * {{number_format($totalBasePriceForRoom, 2)}})</span> {{number_format($totalRoomPrice, 2)}} TK</h2>
                </div>
                <div class="flex justify-between">
                    <p class="text-sm  text-black">Total Night ({{$totalNight}}) </p>
                    <h2 class="text-sm  mb-1 text-black"><span class="text-xs">({{$totalNight}} * {{number_format($totalRoomPrice, 2)}})</span> {{number_format($totalNightPrice, 2)}} TK</h2>
                </div>
                <hr class="text-black font-bold border ">
                <div class="flex justify-between">
                    <p class="text-md font-semibold">Total Amount </p>
                    <h2 class="text-md font-semibold mb-1">{{number_format($totalNightPrice, 2)}} TK</h2>
                </div>
            </div>
        @endforeach
    </div>
    <!-- Comments & Special Request -->
    <div class="rounded-2xl bg-white shadow-md mt-2">
        <h1 class="bg-[#9f825c21] py-1 text-black text-center rounded-t-2xl text-lg font-semibold ">Comments & Requests</h1>
        <div class="p-4 grid grid-cols-2 gap-2">
            <div>
                <p class="text-xs font-semibold">Special Request </p>
                <h2 class="text-md  mb-1 text-black">{{$request}}</h2>
            </div>
            <div>
                <p class="text-xs font-semibold">Comments </p>
                <h2 class="text-md  mb-1 text-black">{{$comment}}</h2>
            </div>
        </div>
    </div>
    <!-- Footer -->
    <div class="bg-white px-2 py-2 my-4 flex items-center">
        <p class="flex-1 text-center text-Black ">&copy;  Obeo Limited.
            All rights reserved.</p>
{{--        <p class="text-end text-black font-semibold text-sm">Printed by: Mohammed Rakib gazi</p>--}}
    </div>
</div>
</body>
</html>
