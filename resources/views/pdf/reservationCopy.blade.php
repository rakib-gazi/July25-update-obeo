{{--@php--}}
{{--    $logoPath = public_path('images/obeologo.png');--}}
{{--    $bookingPath = public_path('images/booking.png');--}}
{{--    $expediaPath = public_path('images/expedia.png');--}}
{{--    $airbnbPath = public_path('images/airbnb.png');--}}
{{--    $cTripPath = public_path('images/ctrip.png');--}}
{{--    $makePath = public_path('images/make.png');--}}



{{--    $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';--}}
{{--    $bookingData = file_exists($bookingPath) ? base64_encode(file_get_contents($bookingPath)) : '';--}}
{{--    $expediaData = file_exists($expediaPath) ? base64_encode(file_get_contents($expediaPath)) : '';--}}
{{--    $airbnbData = file_exists($airbnbPath) ? base64_encode(file_get_contents($airbnbPath)) : '';--}}
{{--    $cTripData = file_exists($cTripPath) ? base64_encode(file_get_contents($cTripPath)) : '';--}}
{{--    $makeData = file_exists($makePath) ? base64_encode(file_get_contents($makePath)) : '';--}}
{{--@endphp--}}
@php
    function imgToBase64($path) {
        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION); // e.g. png, jpg
            $data = file_get_contents($path);
            $base64 = base64_encode($data);
            return "data:image/{$type};base64,{$base64}";
        }
        return '';
    }

    $logoData = imgToBase64(public_path('images/obeologo.png'));
    $bookingData = imgToBase64(public_path('images/booking.png'));
    $expediaData = imgToBase64(public_path('images/expedia.png'));
    $airbnbData = imgToBase64(public_path('images/airbnb.png'));
    $cTripData = imgToBase64(public_path('images/ctrip.png'));
    $makeData = imgToBase64(public_path('images/make.png'));


    $data = [
    "obeo_sl" => 202507027007,
    "reservation_no" => "121211",
    "guest_name" => "Rakib Gazi",
    "check_in" => "2025-07-09",
    "check_out" => "2025-07-18",
    "reservation_date" => "2025-07-23",
    "hotelName" => "Royal Raj Hotel",
    "email" => null,
    "phone" => "01876987622",
    "request" => "This is request",
    "comment" => "this is comment",
    "rooms" => [
        [
            "id" => 47,
            "name" => "Deluxe Single Room",
            "total_room" => 2,
            "total_night" => 2,
            "total_price" => "200.00",
            "currency_id" => 2,
            "currency" => ["id" => 2, "currency" => "USD"]
        ],
        [
            "id" => 48,
            "name" => "Deluxe Double Room",
            "total_room" => 3,
            "total_night" => 4,
            "total_price" => "500.00",
            "currency_id" => 3,
            "currency" => ["id" => 3, "currency" => "BDT"]
        ]
    ],
    "total_adult" => 2,
    "children" => [
        ["id" => 94, "age" => 3],
        ["id" => 95, "age" => 3],
        ["id" => 96, "age" => 2]
    ],
    "total_advance" => "20.00",
    "rate" => "199",
    "currency" => "USD",
    "payment_method" => "Hotel Collects",
    "source" => "Booking.com",
    "total_night" => 2,
    "totalUsd" => "0.00",
    "totalBdt" => "40300.00",
    "totalPayInHotel" => 40280
];


  $obeo_sl = $data['obeo_sl'];
$reservation_no = $data['reservation_no'];
$guest_name = $data['guest_name'];
$check_in = $data['check_in'];
$check_out = $data['check_out'];
$reservation_date = $data['reservation_date'];
$hotelName = $data['hotelName'];
$email = $data['email'];
$phone = $data['phone'];
$request = $data['request'];
$comment = $data['comment'];
$rooms = $data['rooms'];
$total_adult = $data['total_adult'];
$children = $data['children'];
$total_advance = $data['total_advance'];
$rate = $data['rate'];
$currency = $data['currency'];
$payment_method = $data['payment_method'];
$source = $data['source'];
$total_night = $data['total_night'];
$totalUsd = $data['totalUsd'];
$totalBdt = $data['totalBdt'];
$totalPayInHotel = $data['totalPayInHotel'];

@endphp

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>{{$guest_name}}</title>
        <style>
            body {
                font-family: 'nunito';
            }
            .card {
                background-color: #ffffff;
                border-radius: 16px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
                margin-bottom: 16px;
            }
            .header img {
                height: 64px;
            }
            .section-title {
                background-color: #f2efea;
                text-align: center;
                padding: 6px;
                font-size: 16px;
                font-weight: bold;
                border-top-left-radius: 12px;
                border-top-right-radius: 12px;
                color: #000;
            }
            .label {
                font-size: 11px;
                font-weight: bold;
            }
            .value {
                font-size: 13px;
                margin-bottom: 4px;
            }
            hr {
                border: 1px solid #ccc;
            }
            .info-table {
                width: 100%;
                border-collapse: collapse;
                text-align: left;
            }

            .info-table td {
                width: 33%;
                padding: 6px 10px;
                vertical-align: top;
            }

            .label {
                font-weight: bold;
                font-size:  12px ;
                line-height: 1 16px ;
            }

            .value {
                margin-top: 4px;
                font-size: 11pt;
            }
            .room-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }

            .room-table td {
                padding: 4px 8px;
                vertical-align: top;
            }

            .room-name {
                font-weight: bold;
            }

            .text-right {
                text-align: right;
            }

            .total-bold {
                font-weight: bold;
                text-align: right;
            }
            .total-value-bold {
                font-weight: bold;
                text-align: left;
            }
        </style>
    </head>
    <body style="background-color: #f9fafb;">
        <div style=" margin-left: auto; margin-right: auto;">
            <!-- Header -->
           <div class="card" style="padding: 16px">
               <table style="width: 100%; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                border-collapse: collapse;">
                   <tr>
                       <td style="width: 70px; vertical-align: middle;">
                           <img src="{{ $logoData }}" alt="Logo" style="height: 64px;">
                       </td>
                       <td style="vertical-align: middle; text-align: right;">
                           <div>
                               @if ($source === 'Booking.com')
                                   <img src="{{ $bookingData }}" alt="Reservation Source" style="height: 24px;">
                               @elseif ($source === 'Expedia')
                                   <img src="{{ $expediaData }}" alt="Reservation Source" style="height: 32px;">
                               @elseif ($source === 'Airbnb')
                                   <img src="{{ $airbnbData }}" alt="Reservation Source" style="height: 32px;">
                               @elseif ($source === 'Ctrip')
                                   <img src="{{ $cTripData }}" alt="Reservation Source" style="height: 40px;">
                               @elseif ($source === 'Makemytrip')
                                   <img src="{{ $makeData }}" alt="Reservation Source" style="height: 40px;">
                               @else
                                   <h1 style="font-weight: 700; color: #083344; font-size: 1.25rem;">Direct Booking</h1>
                               @endif
                           </div>
                           <h1 style="font-weight: bold; color: #083344; font-size: 1.5rem; margin: 0;">Hotel Reservation</h1>
                           <h3 style="font-weight: 500; color: #083344; font-size: 1.125rem; margin: 0;">{{$hotelName}}</h3>
                       </td>
                   </tr>
               </table>
           </div>
            <!-- reservation Information -->
            <div class="card">
                <div class="section-title">Reservation Information</div>
                <div style="padding: 0 16px 16px;">
                    <table class="info-table" >
                        <tr>
                            <td>
                                <div class="label">Guest Name</div>
                                <div class="value">{{ $guest_name }}</div>
                            </td>
                            <td>
                                <div class="label">Reservation No</div>
                                <div class="value">{{ $reservation_no }}</div>
                            </td>
                            <td>
                                <div class="label">Obeo Sl</div>
                                <div class="value">{{ $obeo_sl }}</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="label">Check In</div>
                                <div class="value">{{ $check_in }}</div>
                            </td>
                            <td>
                                <div class="label">Check Out</div>
                                <div class="value">{{ $check_out }}</div>
                            </td>
                            <td>
                                <div class="label">Booked on</div>
                                <div class="value">{{ $reservation_date }}</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="label">Total Room</div>
                                <div class="value">{{ is_array($rooms) ? count($rooms) : 'N/A' }} Rooms</div>
                            </td>
                            <td>
                                <div class="label">Total Nights</div>
                                <div class="value">{{ $total_night }} Nights</div>
                            </td>
                            <td>
                                <div class="label">Total Adults</div>
                                <div class="value">{{ $total_adult }} Adults</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="label">Childrens & Age ({{ count($children) }})</div>
                                <div class="value">@if (is_array($children) && count($children) > 0)
                                        {{ collect($children)->pluck('age')->implode(', ') }} years
                                    @else
                                        N/A
                                    @endif</div>
                            </td>
                            <td>
                                <div class="label">Phone Number</div>
                                <div class="value">{{ $phone ?? 'N/A' }}</div>
                            </td>
                            <td>
                                <div class="label">Email</div>
                                <div class="value">{{ $email ?? 'N/A' }}</div>
                            </td>
                        </tr>
                    </table>
                </div>

            </div>
            <!-- payment & pricing -->
            <div class="card">
                <div class="section-title">Payment & Pricing</div>
                <div style="padding: 0 16px 16px;">
                    <table class="info-table" >
                        <tr>
                            <td>
                                <div class="label">Price (USD)</div>
                                <div class="value">${{$totalUsd}}</div>
                            </td>
                            <td>
                                <div class="label">Exchange Rate</div>
                                <div class="value">{{$rate}} TK</div>
                            </td>
                            <td>
                                <div class="label">Total Price (BDT)</div>
                                <div class="value">{{$totalBdt}} TK</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="label">Total Advance</div>
                                <div class="value">{{$total_advance}} TK</div>
                            </td>
                            <td>
                                <div class="label">Total Pay In Hotel</div>
                                <div class="value">{{$totalPayInHotel}} TK</div>
                            </td>
                            <td>
                                <div class="label">Payment Method</div>
                                <div class="value">{{$payment_method}}</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <!-- Room wise information -->
            <div class="card">
                <div class="section-title">Room Wise Information & Price Details</div>
                <div style="padding: 0 16px 16px;">
                    <table class="room-table" >
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
                                $totalBasePriceForRoom = $totalPriceInBdt / $baseRateCount;
                                $totalRoomPrice = $totalRoom * $totalBasePriceForRoom;
                                $totalNightPrice = $totalRoomPrice * $totalNight;
                            @endphp

                                <!-- Room Name + Price -->
                            <tr>
                                <td class="room-name">{{ $roomName }} ({{ $totalRoom }})</td>
                                <td class="text-right">({{ $totalRoom }} * {{ number_format($totalBasePriceForRoom, 0) }}) {{ number_format($totalRoomPrice, 0) }} TK</td>
                            </tr>

                            <!-- Total Night Price -->
                            <tr>
                                <td>Total Night ({{$totalNight}})</td>
                                <td class="text-right">({{ $totalNight }} * {{ number_format($totalRoomPrice, 0) }}) {{ number_format($totalNightPrice, 0) }} TK</td>
                            </tr>

                            <!-- Total Amount -->
                            <tr>
                                <td class="total-value-bold">Total Amount</td>
                                <td class="total-bold">{{ number_format($totalNightPrice, 0) }} TK</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
            <!-- comments & requests -->
            <div class="card">
                <div class="section-title">Comments & Requests</div>
                <div style="padding: 0 16px 16px;">
                    <table class="info-table" >
                    <tr>
                        <td>
                            <div class="label">Special Request</div>
                            <div class="value">{{$request}}</div>
                        </td>
                        <td>
                            <div class="label">Comments</div>
                            <div class="value">{{$comment}}</div>
                        </td>
                    </tr>
                </table>
                </div>
            </div>
            <!-- Footer -->
            <div class="card" style="padding: 16px">
               <p style="text-align: center"> &copy; Obeo Limited. All rights reserved.</p>
            </div>
        </div>
    </body>
</html>
