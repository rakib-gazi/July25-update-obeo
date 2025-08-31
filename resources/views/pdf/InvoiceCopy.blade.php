@php
    use Carbon\Carbon;
    function formatDate($date) {
        return Carbon::parse($date)->format('d M Y'); // e.g. 09 July 2025
    }
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



@endphp
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>{{$month}}</title>
        <style>
            body {
                font-family: 'nunito';
            }
            .font-600 {
                font-family: 'nunito600';
            }
            .nunitoR400 {
                font-family: 'nunitoR400';
            }
            .card {
                background-color: #ffffff;
                border-radius: 16px;
                margin-bottom: 16px;
            }
            .header img {
                height: 64px;
            }
            hr {
                border: 1px solid #ccc;
            }
            .section-title {
                background-color: #f2efea;
                text-align: center;
                padding: 6px;
                font-size: 18px;
                line-height: 28px;
                /*font-weight: 600;*/
                border-top-left-radius: 12px;
                border-top-right-radius: 12px;
                color: black;
            }
            .info-table {
                width: 100%;
                border-collapse: collapse;
                text-align: left;
            }
            .guest-table {
                width: 100%;
                border-collapse: collapse;
                text-align: left;
            }
            .info-table td {
                width: 50%;
                padding: 6px 10px;
                vertical-align: top;
            }
            .label {
                font-weight: normal;
                font-size: 12px;
                line-height: 16px;
            }
        </style>
    </head>
    <body style="background-color: #f9fafb;">
        <div style="margin-left: auto; margin-right: auto;">
            <!-- Header -->
            <div class="card" >
                <div style="width: 100%; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                        border-collapse: collapse; ">
                    <div style="padding: 0 16px 16px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 70px; vertical-align: middle; text-align: left;">
                                    <img src="{{ $logoData }}" alt="Logo" style="height: 64px;">
                                </td>
                                <td style="
                                    vertical-align: middle;
                                    text-align: right;
                                    font-size: 30px;
                                    font-weight: bold;
                                    color: #847662;
                                    ">
                                    Invoice
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Invoice Heading -->
            <div class="card">
                <div style="width: 100%; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                        border-collapse: collapse; ">
                    <div style="padding: 16px;">
                        <table class="info-table" >
                            <tr>
                                <!-- Hotel Info -->
                                <td>
                                    <p style="font-size: 20px;" class="font-600">Bill To</p>
                                    <p style="font-size: 18px;" class="font-600">General Manager</p>
                                    <p style="font-size: 16px;">{{$hotel}}</p>
                                    <p style="font-size: 14px;">{{$hotelAddress}}</p>
                                </td>
                                <!-- Invoice Info -->
                                <td style="padding: 12px; text-align: right;">
                                    <table style="border-collapse: collapse; width: 100%;">
                                        <tr>
                                            <td style=" padding: 0;  font-size: 16px; font-family:'nunito600';">Invoice No:</td>
                                            <td style=" padding: 0;  font-size: 14px; ">
                                                {{ $invoiceNo }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style=" padding: 0;  font-size: 16px; font-family:'nunito600';">Invoice Date:</td>
                                            <td style=" padding: 0;  font-size: 14px; ">
                                                {{ $invoiceDate }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style=" padding: 0;  font-size: 16px; font-family:'nunito600';">Invoice Month:</td>
                                            <td style=" padding: 0;  font-size: 14px; ">
                                                {{ $month }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style=" padding: 0; font-size: 16px; font-family:'nunito600'; ">Amount Due:</td>
                                            <td style=" padding: 0; font-size: 14px; font-family:'nunito600'; ">
                                                10,000.00 TK
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <!-- guest info -->
            <div class="card">
                <div class="section-title">Booking.com (Hotel Collects)</div>
                <div style="width: 100%; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                        border-collapse: collapse; ">
                    <div style="padding: 16px;">
                        <table class="guest-table" style="border-spacing: 0 12px;">
                            <thead style="margin: 0 0 12px 0;">
                                <tr>
                                    <th style="color:#847662; width: 5%;  font-size:14px; text-align: left; padding-bottom:12px;">SN</th>
                                    <th style="color:#847662; width: 13%; font-size:14px; text-align: left; padding-bottom:12px;">C/IN</th>
                                    <th style="color:#847662; width: 13%; font-size:14px; text-align: left; padding-bottom:12px;">C/OUT</th>
                                    <th style="color:#847662; width: 23%; font-size:14px; text-align: left; padding-bottom:12px;">GUEST</th>
                                    <th style="color:#847662; width: 23%; font-size:14px; text-align: left; padding-bottom:12px;">ROOM</th>
                                    <th style="color:#847662; width: 12%; font-size:14px; padding-bottom:12px; text-align: right;">TOTAL</th>
                                    <th style="color:#847662; width: 12%; font-size:14px; padding-bottom:12px; text-align: right;">COMM</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($invoices as $invoice)
                                <tr>
                                    <td class="nunitoR400" style="  text-align: left; font-size:14px;">{{ sprintf('%02d', $loop->iteration) }}</td> <!-- SN -->
                                    <td class="nunitoR400" style="  text-align: left; font-size:14px;">{{ formatDate($invoice['check_in'] )}}</td> <!-- C/IN -->
                                    <td class="nunitoR400" style="  text-align: left; font-size:14px;">{{ formatDate($invoice['check_out']) }}</td> <!-- C/OUT -->
                                    <td class="nunitoR400" style="  text-align: left; font-size:14px;">{{ $invoice['guest_name'] ?? '-' }}</td> <!-- Guest -->
                                    <td class="nunitoR400" style="  text-align: left; font-size:14px;">
                                        @if(!empty($invoice['hotel_invoice_rooms']))
                                            @foreach($invoice['hotel_invoice_rooms'] as $room)
                                                <div>{{ $room['room_name'] }} (x{{ $room['total_room'] }})</div>
                                            @endforeach
                                        @endif
                                    </td> <!-- Guest -->
                                    <td class="nunitoR400" style="  text-align: right; font-size:14px;">
                                        {{ number_format($invoice['total_amount'] ?? 0, 2) }}
                                    </td> <!-- Price -->
                                    <td class="nunitoR400" style="  text-align: right; font-size:14px;">
                                        {{ number_format(($invoice['total_amount'] * $invoice['hotelCollectsCommission']) / 100, 2) }}
                                    </td> <!-- Comm -->
                                </tr>
                            @endforeach
                            @php
                                $grandTotal = collect($invoices)->sum('total_amount');
                                $grandCommission = collect($invoices)->sum(function($inv) {
                                    return ($inv['total_amount'] * $inv['hotelCollectsCommission']) / 100;
                                });
                            @endphp
                            <tr style="padding: 10px 0">
                                <td colspan="5" class="nunitoR400" style=" color:#847662; text-align: right; font-size:14px; font-weight: bold;">
                                    Total
                                </td>
                                <td class="nunitoR400" style=" color:#847662; text-align: right; font-size:14px; font-weight: bold;">
                                    {{ number_format($grandTotal, 2) }}
                                </td>
                                <td class="nunitoR400" style=" color:#847662; text-align: right; font-size:14px; font-weight: bold;">
                                    {{ number_format($grandCommission, 2) }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>


            <!-- Footer -->
            <div class="card" style="padding: 12px">
                <p style="text-align: center"> &copy; Obeo Limited. All rights reserved.</p>
            </div>
        </div>
    </body>
</html>

