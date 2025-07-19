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
    <style>
        body {
            background-color: #f9fafb;
            font-family: Arial, sans-serif;
            color: #111827;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 16px;
        }
        .card {
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 16px;
            padding: 16px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header img {
            height: 64px;
        }
        .text-right {
            text-align: right;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
        }
        .subtitle {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
        }
        .section-title {
            background-color: #9f825c21;
            text-align: center;
            padding: 6px;
            font-size: 16px;
            font-weight: bold;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            color: #000;
        }
        .grid {
            display: flex;
            flex-wrap: wrap;
        }
        .col-3 {
            width: 33.33%;
            box-sizing: border-box;
            padding: 6px;
        }
        .col-2 {
            width: 50%;
            box-sizing: border-box;
            padding: 6px;
        }
        .label {
            font-size: 11px;
            font-weight: bold;
        }
        .value {
            font-size: 13px;
            margin-bottom: 4px;
        }
        .footer {
            background-color: #fff;
            padding: 8px;
            text-align: center;
            margin-top: 20px;
        }
        hr {
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card header">
        <img src="data:image/png;base64,{{ $logoData }}" alt="Logo">
        <div class="text-right">
            <div>
                @if ($source === 'Booking.com')
                    <img src="data:image/png;base64,{{ $bookingData }}" alt="Reservation Source" style="height: 24px;">
                @elseif ($source === 'Expedia')
                    <img src="data:image/png;base64,{{ $expediaData }}" alt="Reservation Source" style="height: 32px;">
                @elseif ($source === 'Airbnb')
                    <img src="data:image/png;base64,{{ $airbnbData }}" alt="Reservation Source" style="height: 32px;">
                @elseif ($source === 'Ctrip')
                    <img src="data:image/png;base64,{{ $cTripData }}" alt="Reservation Source" style="height: 40px;">
                @elseif ($source === 'Makemytrip')
                    <img src="data:image/png;base64,{{ $makeData }}" alt="Reservation Source" style="height: 40px;">
                @else
                    <h1 class="title">Direct Booking</h1>
                @endif
            </div>
            <h1 class="title">Hotel Reservation</h1>
            <h3 class="subtitle">{{$hotelName}}</h3>
        </div>
    </div>

    <div class="card">
        <div class="section-title">Reservation Information</div>
        <div class="grid">
            <div class="col-3"><p class="label">Guest Name</p><div class="value">{{$guest_name}}</div></div>
            <div class="col-3"><p class="label">Reservation No</p><div class="value">{{$reservation_no}}</div></div>
            <div class="col-3"><p class="label">Obeo Sl</p><div class="value">{{$obeo_sl}}</div></div>
            <div class="col-3"><p class="label">Check In</p><div class="value">{{$check_in}}</div></div>
            <div class="col-3"><p class="label">Check Out</p><div class="value">{{$check_out}}</div></div>
            <div class="col-3"><p class="label">Booked on</p><div class="value">{{$reservation_date}}</div></div>
            <div class="col-3"><p class="label">Total Room</p><div class="value">{{ is_array($rooms) ? count($rooms) : 'N/A' }} Rooms</div></div>
            <div class="col-3"><p class="label">Total Nights</p><div class="value">{{$total_night}} Nights</div></div>
            <div class="col-3"><p class="label">Total Adults</p><div class="value">{{$total_adult}} Adults</div></div>
            <div class="col-3"><p class="label">Childrens & Age ({{ count($children) }})</p>
                <div class="value">
                    @if (is_array($children) && count($children) > 0)
                        {{ collect($children)->pluck('age')->implode(', ') }} years
                    @else
                        N/A
                    @endif
                </div>
            </div>
            <div class="col-3"><p class="label">Phone Number</p><div class="value">{{$phone ?? 'N/A'}}</div></div>
            <div class="col-3"><p class="label">Email</p><div class="value">{{$email ?? 'N/A'}}</div></div>
        </div>
    </div>

    <div class="card">
        <div class="section-title">Payment & Pricing</div>
        <div class="grid">
            <div class="col-3"><p class="label">Price (USD)</p><div class="value">${{$totalUsd}}</div></div>
            <div class="col-3"><p class="label">Exchange Rate</p><div class="value">{{$rate}} TK</div></div>
            <div class="col-3"><p class="label">Total Price (BDT)</p><div class="value">{{$totalBdt}} TK</div></div>
            <div class="col-3"><p class="label">Total Advance</p><div class="value">{{$total_advance}} TK</div></div>
            <div class="col-3"><p class="label">Total Pay In Hotel</p><div class="value">{{$totalPayInHotel}} TK</div></div>
            <div class="col-3"><p class="label">Payment Method</p><div class="value">{{$payment_method}}</div></div>
        </div>
    </div>

    <div class="card">
        <div class="section-title">Room Wise Information & Price Details</div>
        @foreach($rooms as $room)
            @php
                $roomName = $room['name'];
                $totalRoom = $room['total_room'];
                $totalNight = $room['total_night'];
                $totalPrice = (float)$room['total_price'];
                $rate = (float) $rate;
                $baseRateCount = $totalRoom * $totalNight;
                $currency = $room['currency']['currency'];
                $totalPriceInBdt = $currency === 'USD' ?  $totalPrice * $rate : $totalPrice;
                $totalBasePriceForRoom = $totalPriceInBdt/$baseRateCount;
                $totalRoomPrice = $totalRoom * $totalBasePriceForRoom;
                $totalNightPrice= $totalRoomPrice * $totalNight;
            @endphp
            <div>
                <p><strong>{{$roomName}} ({{$totalRoom}})</strong> - ({{$totalRoom}} * {{number_format($totalBasePriceForRoom, 2)}}) = {{number_format($totalRoomPrice, 2)}} TK</p>
                <p>Total Night ({{$totalNight}}) - ({{$totalNight}} * {{number_format($totalRoomPrice, 2)}}) = {{number_format($totalNightPrice, 2)}} TK</p>
                <hr>
                <p><strong>Total Amount:</strong> {{number_format($totalNightPrice, 2)}} TK</p>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="section-title">Comments & Requests</div>
        <div class="grid">
            <div class="col-2">
                <p class="label">Special Request</p>
                <div class="value">{{$request}}</div>
            </div>
            <div class="col-2">
                <p class="label">Comments</p>
                <div class="value">{{$comment}}</div>
            </div>
        </div>
    </div>

    <div class="footer">
        &copy; Obeo Limited. All rights reserved.
    </div>
</div>
</body>
</html>
