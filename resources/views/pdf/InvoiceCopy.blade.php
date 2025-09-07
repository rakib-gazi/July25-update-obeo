@php
    use Carbon\Carbon;
//    For Date Formating
    function formatDate($date) {
        return Carbon::parse($date)->format('d M Y'); // e.g. 09 July 2025
    }

//    for image print
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


    // Make sure $invoices is an array
if (!isset($invoices)) {
    $invoices = [];
} elseif (is_string($invoices)) {
    $invoices = json_decode($invoices, true) ?: [];
} elseif (!is_array($invoices)) {
    $invoices = (array) $invoices;
}

// Ensure these variables exist
$downloadType = $downloadType ?? '';
$hotelCollectsCommission = $hotelCollectsCommission ?? 0;
$expediaCollectsCommission = $expediaCollectsCommission ?? 0;


// Ensure adjustments exist
if (!isset($monthlyAdjustments)) {
    $monthlyAdjustments = collect();
} elseif (is_array($monthlyAdjustments)) {
    $monthlyAdjustments = collect($monthlyAdjustments);
}



// Booking.com hotel collects section
$bookingInvoices = collect($invoices)->filter(fn($inv) => ($inv['source'] ?? '') === 'Booking.com' &&
        ($inv['payment_method'] ?? '') === 'Hotel Collects')->values();

$grandTotal = $bookingInvoices->sum(fn($inv) => (float)($inv['total_amount'] ?? 0));
$grandCommission = $bookingInvoices->sum(fn($inv) => (float)($inv['total_amount'] ?? 0) * ((float)($inv['hotelCollectsCommission'] ?? $hotelCollectsCommission)) / 100);
$bookingComHotelCollectsCommission = optional($bookingInvoices->first())['hotelCollectsCommission'] ?? $hotelCollectsCommission;



//Expedia Hotel collects Section
$expediaInvoices = collect($invoices)->filter(fn($inv) => ($inv['source'] ?? '') === 'Expedia' &&
        ($inv['payment_method'] ?? '') === 'Hotel Collects')->values();

$expediaTotal = $expediaInvoices->sum(fn($inv) => (float)($inv['total_amount'] ?? 0));
$expediaCommission = $expediaInvoices->sum(fn($inv) => (float)($inv['total_amount'] ?? 0) * ((float)($inv['hotelCollectsCommission'] ?? $hotelCollectsCommission)) / 100);
$expediaHotelCollectsCommission = optional($expediaInvoices->first())['hotelCollectsCommission'] ?? $hotelCollectsCommission;


//Expedia Expedia-collects Section
$expediaCollectsInvoices = collect($invoices)->filter(fn($inv) => ($inv['source'] ?? '') === 'Expedia' &&
        ($inv['payment_method'] ?? '') === 'Expedia Collects')->values();

$expediaCollectsTotal = $expediaCollectsInvoices->sum(fn($inv) => (float)($inv['total_amount'] ?? 0));
$expediaCollectsCommission = $expediaCollectsInvoices->sum(fn($inv) => (float)($inv['total_amount'] ?? 0) * ((float)($inv['expediaCollectsCommission'] ?? $expediaCollectsCommission)) / 100);
$expediaExpediaCollectsCommission = optional($expediaCollectsInvoices->first())['expediaCollectsCommission'] ?? $expediaCollectsCommission;

//booking.com calculation
$finalGrandTotal = 0;

    if ($downloadType === "Booking.com" && $bookingInvoices->isNotEmpty()) {
        $finalGrandTotal = $grandCommission;

        foreach ($monthlyAdjustments as $adjustment) {
            if ($adjustment->source === "Booking.com") {
                if ($adjustment->type === "Debit") {
                    $finalGrandTotal -= $adjustment->amount;
                } else {
                    $finalGrandTotal += $adjustment->amount;
                }
            }
        }
    }


//Expedia hotel collects calculation
$expediaFinalGrandTotal = 0;

    if ($downloadType === "expediaHotelCollects" && $expediaInvoices->isNotEmpty()) {
        $expediaFinalGrandTotal = $expediaCommission;

        foreach ($monthlyAdjustments as $adjustment) {
            if ($adjustment->source === "expediaHotelCollects") {
                if ($adjustment->type === "Debit") {
                    $expediaFinalGrandTotal -= $adjustment->amount;
                } else {
                    $expediaFinalGrandTotal += $adjustment->amount;
                }
            }
        }
    }
//Expedia expedia collects calculation
$expediaCollectsFinalGrandTotal = 0;
$expediaCollectsData = [];

if ($downloadType === "expediaCollects" && $expediaCollectsInvoices->isNotEmpty()) {
    // Start from total
    $expediaCollectsFinalGrandTotal = $expediaCollectsTotal;

    // Row 1: Total Expedia Collects Amount
    $expediaCollectsData[] = [
        'description' => "Total Expedia Collects Amount",
        'type'        => "Credit",
        'amount'      => $expediaCollectsTotal,
        'total'       => $expediaCollectsFinalGrandTotal,
    ];

    // Row 2: Commission
    $expediaCollectsFinalGrandTotal -= $expediaCollectsCommission;
    $expediaCollectsData[] = [
        'description' => "Expedia collects Commission ({$expediaExpediaCollectsCommission}%)",
        'type'        => "Debit",
        'amount'      => $expediaCollectsCommission,
        'total'       => $expediaCollectsFinalGrandTotal,
    ];

    // Row 3+: Dynamic Adjustments
    foreach ($monthlyAdjustments as $adjustment) {
        if ($adjustment->source === "expediaCollects") {
            if ($adjustment->type === "Debit") {
                $expediaCollectsFinalGrandTotal -= $adjustment->amount;
            } else {
                $expediaCollectsFinalGrandTotal += $adjustment->amount;
            }

            $expediaCollectsData[] = [
                'description' => $adjustment->purpose ?? 'Adjustment',
                'type'        => $adjustment->type,
                'amount'      => $adjustment->amount,
                'total'       => $expediaCollectsFinalGrandTotal,
            ];
        }
    }
}

//combined
$combinedGrandTotal = 0;
$combinedData = [];

if ($downloadType === "Combined") {

    // -------------------------
    // Step 1: Expedia Collects (if exists)
    // -------------------------
    if ($expediaCollectsInvoices->isNotEmpty()) {
        // Row 1: Total Expedia Collects Amount
        $combinedGrandTotal = $expediaCollectsTotal;
        $combinedData[] = [
            'description' => "Total Expedia Collects Amount",
            'type'        => "Credit",
            'amount'      => $expediaCollectsTotal,
            'total'       => $combinedGrandTotal,
        ];

        // Row 2: Subtract Expedia Collects Commission
        $combinedGrandTotal -= $expediaCollectsCommission;
        $combinedData[] = [
            'description' => "Expedia Collects Commission ({$expediaExpediaCollectsCommission}%)",
            'type'        => "Debit",
            'amount'      => $expediaCollectsCommission,
            'total'       => $combinedGrandTotal,
        ];
    }

    // -------------------------
    // Step 2: Expedia Hotel Collects
    // -------------------------
    if ($expediaInvoices->isNotEmpty()) {
        if ($expediaCollectsInvoices->isNotEmpty()) {
            // If Expedia Collects exists → subtract
            $combinedGrandTotal -= $expediaCommission;
            $combinedData[] = [
                'description' => "Expedia Hotel Collects Commission ({$expediaHotelCollectsCommission}%)",
                'type'        => "Debit",
                'amount'      => $expediaCommission,
                'total'       => $combinedGrandTotal,
            ];
        } else {
            // If no Expedia Collects → treat as Credit
            $combinedGrandTotal += $expediaCommission;
            $combinedData[] = [
                'description' => "Expedia Hotel Collects Commission ({$expediaHotelCollectsCommission}%)",
                'type'        => "Credit",
                'amount'      => $expediaCommission,
                'total'       => $combinedGrandTotal,
            ];
        }
    }

    // -------------------------
    // Step 3: Booking.com
    // -------------------------
    if ($bookingInvoices->isNotEmpty()) {
        if ($expediaCollectsInvoices->isNotEmpty()) {
            // If Expedia Collects exists → subtract
            $combinedGrandTotal -= $grandCommission;
            $combinedData[] = [
                'description' => "Booking.com Commission ({$bookingComHotelCollectsCommission}%)",
                'type'        => "Debit",
                'amount'      => $grandCommission,
                'total'       => $combinedGrandTotal,
            ];
        } else {
            // If no Expedia Collects → treat as Credit
            $combinedGrandTotal += $grandCommission;
            $combinedData[] = [
                'description' => "Booking.com Commission ({$bookingComHotelCollectsCommission}%)",
                'type'        => "Credit",
                'amount'      => $grandCommission,
                'total'       => $combinedGrandTotal,
            ];
        }
    }

    // -------------------------
    // Step 4: Apply Adjustments
    // -------------------------
    foreach ($monthlyAdjustments as $adjustment) {
        if ($adjustment->source === "Combined") {
            if ($adjustment->type === "Debit") {
                $combinedGrandTotal -= $adjustment->amount;
            } else {
                $combinedGrandTotal += $adjustment->amount;
            }

            $combinedData[] = [
                'description' => $adjustment->purpose ?? 'Adjustment',
                'type'        => $adjustment->type,
                'amount'      => $adjustment->amount,
                'total'       => $combinedGrandTotal,
            ];
        }
    }
}




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

                                            @if($downloadType === "Booking.com" && $bookingInvoices->isNotEmpty())
                                                <td style=" padding: 0; font-size: 16px; font-family:'nunito600'; color:red">Amount Due:</td>
                                                <td style=" padding: 0; font-size: 14px; font-family:'nunito600'; color:red">  {{number_format($finalGrandTotal, 2)}} </td>
                                            @elseif($downloadType === "expediaHotelCollects" && $expediaInvoices->isNotEmpty())
                                                <td style=" padding: 0; font-size: 16px; font-family:'nunito600'; color:red">Amount Due:</td>
                                                <td style=" padding: 0; font-size: 14px; font-family:'nunito600'; color:red">  {{number_format($expediaFinalGrandTotal, 2)}} </td>
                                            @elseif($downloadType === "expediaCollects" && $expediaCollectsInvoices->isNotEmpty())
                                                <td style=" padding: 0; font-size: 16px; font-family:'nunito600'; color:red">Payable To Hotel:</td>
                                                <td style=" padding: 0; font-size: 14px; font-family:'nunito600'; color:red">  {{number_format($expediaCollectsFinalGrandTotal, 2)}} </td>
                                            @endif

                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <!-- booking.com hotel collects guest info -->
            @if ($downloadType === "Booking.com" && $bookingInvoices->isNotEmpty())
                <div class="card">
                    <div class="section-title">Booking.com (Hotel Collects)</div>
                    <div style="width: 100%; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                        border-collapse: collapse; ">
                        <div style="padding: 16px;">
                            <table class="guest-table" style="border-spacing: 0 12px;">
                                <thead style="margin: 0 0 12px 0;">
                                <tr>
                                    <th style="color:#847662; width: 4%;  font-size:14px; text-align: left; padding-bottom:12px;">SN</th>
                                    <th style="color:#847662; width: 14%; font-size:14px; text-align: left; padding-bottom:12px;">C/IN</th>
                                    <th style="color:#847662; width: 14%; font-size:14px; text-align: left; padding-bottom:12px;">C/OUT</th>
                                    <th style="color:#847662; width: 22%; font-size:14px; text-align: left; padding-bottom:12px;">GUEST</th>
                                    <th style="color:#847662; width: 23%; font-size:14px; text-align: left; padding-bottom:12px;">ROOM</th>
                                    <th style="color:#847662; width: 12%; font-size:14px; padding-bottom:12px; text-align: right;">TOTAL</th>
                                    <th style="color:#847662; width: 12%; font-size:14px; padding-bottom:12px; text-align: right;">COMM</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($bookingInvoices  as $invoice)
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
                                    <tr>
                                        <td colspan="5" class="nunitoR400" style="padding: 16px 0 0 0 ; color:#847662; text-align: right; font-size:14px; font-weight: bold;">
                                            Total
                                        </td>
                                        <td class="nunitoR400" style="padding: 16px 0 0 0 ; color:#847662; text-align: right; font-size:14px; font-weight: bold;">
                                            {{ number_format($grandTotal, 2) }}
                                        </td>
                                        <td class="nunitoR400" style="padding: 16px 0 0 0 ; color:#847662; text-align: right; font-size:14px; font-weight: bold;">
                                            {{ number_format($grandCommission, 2) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="card">
                    @php
                        // start with commission as base grand total
                        $finalGrandTotal = $grandCommission;
                    @endphp
                    <div class="section-title">Invoice Summary</div>
                    <div style="width: 100%; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                        border-collapse: collapse; ">
                        <div style="padding: 16px;">
                            <table class="guest-table" style="border-spacing: 0 12px;">
                                <thead style="margin: 0 0 12px 0;">
                                <tr>
                                    <th style="color:#847662; width: 4%;  font-size:14px; text-align: left; padding-bottom:12px;">SN</th>
                                    <th style="color:#847662; width: 26%; font-size:14px; text-align: left; padding-bottom:12px;">Description</th>
                                    <th style="color:#847662; width: 15%; font-size:14px; text-align: left; padding-bottom:12px;">Debit / Credit</th>
                                    <th style="color:#847662; width: 15%; font-size:14px;  padding-bottom:12px; text-align: right;">Amount (TK)</th>
                                    <th style="color:#847662; width: 20%; font-size:14px;  padding-bottom:12px; text-align: right;">Total (TK)</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="nunitoR400" style="  text-align: left; font-size:14px;">01</td>
                                    <td class="nunitoR400" style="  text-align: left; font-size:14px;">Booking.com Commission  ({{ $bookingComHotelCollectsCommission }}%)</td>
                                    <td class="nunitoR400" style="  text-align: left; font-size:14px;">Credit</td>
                                    <td class="nunitoR400" style="  text-align: right; font-size:14px;">{{ number_format($grandCommission, 2) }}</td>
{{--                                    Grand Total--}}
                                    <td class="nunitoR400" style="  text-align: right; font-size:14px;">{{ number_format($finalGrandTotal, 2) }}</td>
                                </tr>
                                @foreach($monthlyAdjustments as $adjustment)
                                    @if($adjustment->source === "Booking.com")
                                        @php
                                            // update running total
                                            if ($adjustment->type === "Debit") {
                                                $finalGrandTotal -= $adjustment->amount;
                                            } else {
                                                $finalGrandTotal += $adjustment->amount;
                                            }
                                        @endphp
                                        <tr>
                                            <td class="nunitoR400" style="text-align: left; font-size:14px;">{{ sprintf('%02d', $loop->iteration + 1) }}</td>
                                            <td class="nunitoR400" style="text-align: left; font-size:14px;">{{ $adjustment->purpose }}</td>
                                            <td class="nunitoR400" style="text-align: left; font-size:14px;">{{ $adjustment->type }}</td>
                                            <td class="nunitoR400" style="text-align: right; font-size:14px;">{{ number_format($adjustment->amount, 2) }}</td>
                                            <td class="nunitoR400" style="text-align: right; font-size:14px;">{{ number_format($finalGrandTotal, 2) }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                                        <tr>
                                            <td colspan="4" class="nunitoR400" style="padding: 16px 0 0 0 ; color:red; text-align: right; font-size:14px; ">
                                                Total Due
                                            </td>
                                            <td class="nunitoR400" style="padding: 16px 0 0 0 ; color:red; text-align: right; font-size:14px; ">
                                                {{ number_format($finalGrandTotal, 2) }}
                                            </td>
                                        </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            @endif
            <!-- Expedia hotel collects guest info -->
            @if ($downloadType === "expediaHotelCollects" && $expediaInvoices->isNotEmpty())
                <div class="card">
                    <div class="section-title">Expedia (Hotel Collects)</div>
                    <div style="width: 100%; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                        border-collapse: collapse; ">
                        <div style="padding: 16px;">
                            <table class="guest-table" style="border-spacing: 0 12px;">
                                <thead style="margin: 0 0 12px 0;">
                                <tr>
                                    <th style="color:#847662; width: 4%;  font-size:14px; text-align: left; padding-bottom:12px;">SN</th>
                                    <th style="color:#847662; width: 14%; font-size:14px; text-align: left; padding-bottom:12px;">C/IN</th>
                                    <th style="color:#847662; width: 14%; font-size:14px; text-align: left; padding-bottom:12px;">C/OUT</th>
                                    <th style="color:#847662; width: 22%; font-size:14px; text-align: left; padding-bottom:12px;">GUEST</th>
                                    <th style="color:#847662; width: 23%; font-size:14px; text-align: left; padding-bottom:12px;">ROOM</th>
                                    <th style="color:#847662; width: 12%; font-size:14px; padding-bottom:12px; text-align: right;">TOTAL</th>
                                    <th style="color:#847662; width: 12%; font-size:14px; padding-bottom:12px; text-align: right;">COMM</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($expediaInvoices   as $invoice)
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
                                    <tr>
                                        <td colspan="5" class="nunitoR400" style="padding: 16px 0 0 0 ; color:#847662; text-align: right; font-size:14px; font-weight: bold;">
                                            Total
                                        </td>
                                        <td class="nunitoR400" style="padding: 16px 0 0 0 ; color:#847662; text-align: right; font-size:14px; font-weight: bold;">
                                            {{ number_format($expediaTotal, 2) }}
                                        </td>
                                        <td class="nunitoR400" style="padding: 16px 0 0 0 ; color:#847662; text-align: right; font-size:14px; font-weight: bold;">
                                            {{ number_format($expediaCommission, 2) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    @php
                        // start with commission as base grand total
                        $expediaFinalGrandTotal = $expediaCommission;
                    @endphp
                    <div class="section-title">Invoice Summary</div>
                    <div style="width: 100%; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                        border-collapse: collapse; ">
                        <div style="padding: 16px;">
                            <table class="guest-table" style="border-spacing: 0 12px;">
                                <thead style="margin: 0 0 12px 0;">
                                <tr>
                                    <th style="color:#847662; width: 4%;  font-size:14px; text-align: left; padding-bottom:12px;">SN</th>
                                    <th style="color:#847662; width: 26%; font-size:14px; text-align: left; padding-bottom:12px;">Description</th>
                                    <th style="color:#847662; width: 15%; font-size:14px; text-align: left; padding-bottom:12px;">Debit / Credit</th>
                                    <th style="color:#847662; width: 15%; font-size:14px;  padding-bottom:12px; text-align: right;">Amount (TK)</th>
                                    <th style="color:#847662; width: 20%; font-size:14px;  padding-bottom:12px; text-align: right;">Total (TK)</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="nunitoR400" style="  text-align: left; font-size:14px;">01</td>
                                    <td class="nunitoR400" style="  text-align: left; font-size:14px;">Expedia Hotel collects Commission  ({{ $expediaHotelCollectsCommission }}%)</td>
                                    <td class="nunitoR400" style="  text-align: left; font-size:14px;">Credit</td>
                                    <td class="nunitoR400" style="  text-align: right; font-size:14px;">{{ number_format($expediaCommission, 2) }}</td>
                                    {{--                                    Grand Total--}}
                                    <td class="nunitoR400" style="  text-align: right; font-size:14px;">{{ number_format($expediaFinalGrandTotal, 2) }}</td>
                                </tr>
                                @foreach($monthlyAdjustments as $adjustment)
                                    @if($adjustment->source === "expediaHotelCollects")
                                        @php
                                            // update running total
                                            if ($adjustment->type === "Debit") {
                                                $expediaFinalGrandTotal -= $adjustment->amount;
                                            } else {
                                                $expediaFinalGrandTotal += $adjustment->amount;
                                            }
                                        @endphp
                                        <tr>
                                            <td class="nunitoR400" style="text-align: left; font-size:14px;">{{ sprintf('%02d', $loop->iteration + 1) }}</td>
                                            <td class="nunitoR400" style="text-align: left; font-size:14px;">{{ $adjustment->purpose }}</td>
                                            <td class="nunitoR400" style="text-align: left; font-size:14px;">{{ $adjustment->type }}</td>
                                            <td class="nunitoR400" style="text-align: right; font-size:14px;">{{ number_format($adjustment->amount, 2) }}</td>
                                            <td class="nunitoR400" style="text-align: right; font-size:14px;">{{ number_format($expediaFinalGrandTotal, 2) }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                                <tr>
                                    <td colspan="4" class="nunitoR400" style="padding: 16px 0 0 0 ; color:red; text-align: right; font-size:14px; ">
                                        Total Due
                                    </td>
                                    <td class="nunitoR400" style="padding: 16px 0 0 0 ; color:red; text-align: right; font-size:14px; ">
                                        {{ number_format($expediaFinalGrandTotal, 2) }}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            @endif
            <!-- Expedia expedia-collects guest info -->
            @if ($downloadType === "expediaCollects" && $expediaCollectsInvoices->isNotEmpty())
                <div class="card">
                    <div class="section-title">Expedia (Expedia Collects)</div>
                    <div style="width: 100%; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                        border-collapse: collapse; ">
                        <div style="padding: 16px;">
                            <table class="guest-table" style="border-spacing: 0 12px;">
                                <thead style="margin: 0 0 12px 0;">
                                <tr>
                                    <th style="color:#847662; width: 4%;  font-size:14px; text-align: left; padding-bottom:12px;">SN</th>
                                    <th style="color:#847662; width: 14%; font-size:14px; text-align: left; padding-bottom:12px;">C/IN</th>
                                    <th style="color:#847662; width: 14%; font-size:14px; text-align: left; padding-bottom:12px;">C/OUT</th>
                                    <th style="color:#847662; width: 22%; font-size:14px; text-align: left; padding-bottom:12px;">GUEST</th>
                                    <th style="color:#847662; width: 23%; font-size:14px; text-align: left; padding-bottom:12px;">ROOM</th>
                                    <th style="color:#847662; width: 12%; font-size:14px; padding-bottom:12px; text-align: right;">TOTAL</th>
                                    <th style="color:#847662; width: 12%; font-size:14px; padding-bottom:12px; text-align: right;">COMM</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($expediaCollectsInvoices   as $invoice)
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
                                                {{ number_format(($invoice['total_amount'] * $invoice['expediaCollectsCommission']) / 100, 2) }}
                                            </td> <!-- Comm -->
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="5" class="nunitoR400" style="padding: 16px 0 0 0 ; color:#847662; text-align: right; font-size:14px; font-weight: bold;">
                                            Total
                                        </td>
                                        <td class="nunitoR400" style="padding: 16px 0 0 0 ; color:#847662; text-align: right; font-size:14px; font-weight: bold;">
                                            {{ number_format($expediaCollectsTotal, 2) }}
                                        </td>
                                        <td class="nunitoR400" style="padding: 16px 0 0 0 ; color:#847662; text-align: right; font-size:14px; font-weight: bold;">
                                            {{ number_format($expediaCollectsCommission, 2) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="section-title">Invoice Summary</div>
                    <div style="width: 100%; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                        border-collapse: collapse; ">
                        <div style="padding: 16px;">
                            <table class="guest-table" style="border-spacing: 0 12px;">
                                <thead style="margin: 0 0 12px 0;">
                                <tr>
                                    <th style="color:#847662; width: 4%;  font-size:14px; text-align: left; padding-bottom:12px;">SN</th>
                                    <th style="color:#847662; width: 26%; font-size:14px; text-align: left; padding-bottom:12px;">Description</th>
                                    <th style="color:#847662; width: 15%; font-size:14px; text-align: left; padding-bottom:12px;">Debit / Credit</th>
                                    <th style="color:#847662; width: 15%; font-size:14px;  padding-bottom:12px; text-align: right;">Amount (TK)</th>
                                    <th style="color:#847662; width: 20%; font-size:14px;  padding-bottom:12px; text-align: right;">Total (TK)</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($expediaCollectsData as $index => $row)
                                            <tr>
                                                <td class="nunitoR400" style="text-align: left; font-size:14px;">{{ sprintf('%02d',  str_pad($index+1, 2, '0', STR_PAD_LEFT) ) }}</td>
                                                <td class="nunitoR400" style="text-align: left; font-size:14px;">{{ $row['description'] }}</td>
                                                <td class="nunitoR400" style="text-align: left; font-size:14px;">{{ $row['type'] }}</td>
                                                <td class="nunitoR400" style="text-align: right; font-size:14px;">{{ number_format($row['amount'], 2) }}</td>
                                                <td class="nunitoR400" style="text-align: right; font-size:14px;">{{ number_format($row['total'], 2) }}</td>
                                            </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="4" class="nunitoR400" style="padding: 16px 0 0 0 ; color:red; text-align: right; font-size:14px; ">
                                            Total Payable To Hotel
                                        </td>
                                        <td class="nunitoR400" style="padding: 16px 0 0 0 ; color:red; text-align: right; font-size:14px; ">
                                            {{ number_format($expediaCollectsFinalGrandTotal, 2) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            @endif
            <!-- Expedia expedia-collects guest info -->
            @if ($downloadType === "Combined")
                @if($bookingInvoices->isNotEmpty())
                    <div class="card">
                        <div class="section-title">Booking.com (Hotel Collects)</div>
                        <div style="width: 100%; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                        border-collapse: collapse; ">
                            <div style="padding: 16px;">
                                <table class="guest-table" style="border-spacing: 0 12px;">
                                    <thead style="margin: 0 0 12px 0;">
                                    <tr>
                                        <th style="color:#847662; width: 4%;  font-size:14px; text-align: left; padding-bottom:12px;">SN</th>
                                        <th style="color:#847662; width: 14%; font-size:14px; text-align: left; padding-bottom:12px;">C/IN</th>
                                        <th style="color:#847662; width: 14%; font-size:14px; text-align: left; padding-bottom:12px;">C/OUT</th>
                                        <th style="color:#847662; width: 22%; font-size:14px; text-align: left; padding-bottom:12px;">GUEST</th>
                                        <th style="color:#847662; width: 23%; font-size:14px; text-align: left; padding-bottom:12px;">ROOM</th>
                                        <th style="color:#847662; width: 12%; font-size:14px; padding-bottom:12px; text-align: right;">TOTAL</th>
                                        <th style="color:#847662; width: 12%; font-size:14px; padding-bottom:12px; text-align: right;">COMM</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($bookingInvoices  as $invoice)
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
                                    <tr>
                                        <td colspan="5" class="nunitoR400" style="padding: 16px 0 0 0 ; color:#847662; text-align: right; font-size:14px; font-weight: bold;">
                                            Total
                                        </td>
                                        <td class="nunitoR400" style="padding: 16px 0 0 0 ; color:#847662; text-align: right; font-size:14px; font-weight: bold;">
                                            {{ number_format($grandTotal, 2) }}
                                        </td>
                                        <td class="nunitoR400" style="padding: 16px 0 0 0 ; color:#847662; text-align: right; font-size:14px; font-weight: bold;">
                                            {{ number_format($grandCommission, 2) }}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                @endif
                @if($expediaInvoices->isNotEmpty())
                    <div class="card">
                        <div class="section-title">Expedia (Hotel Collects)</div>
                        <div style="width: 100%; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                    border-collapse: collapse; ">
                            <div style="padding: 16px;">
                                <table class="guest-table" style="border-spacing: 0 12px;">
                                    <thead style="margin: 0 0 12px 0;">
                                    <tr>
                                        <th style="color:#847662; width: 4%;  font-size:14px; text-align: left; padding-bottom:12px;">SN</th>
                                        <th style="color:#847662; width: 14%; font-size:14px; text-align: left; padding-bottom:12px;">C/IN</th>
                                        <th style="color:#847662; width: 14%; font-size:14px; text-align: left; padding-bottom:12px;">C/OUT</th>
                                        <th style="color:#847662; width: 22%; font-size:14px; text-align: left; padding-bottom:12px;">GUEST</th>
                                        <th style="color:#847662; width: 23%; font-size:14px; text-align: left; padding-bottom:12px;">ROOM</th>
                                        <th style="color:#847662; width: 12%; font-size:14px; padding-bottom:12px; text-align: right;">TOTAL</th>
                                        <th style="color:#847662; width: 12%; font-size:14px; padding-bottom:12px; text-align: right;">COMM</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($expediaInvoices   as $invoice)
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
                                    <tr>
                                        <td colspan="5" class="nunitoR400" style="padding: 16px 0 0 0 ; color:#847662; text-align: right; font-size:14px; font-weight: bold;">
                                            Total
                                        </td>
                                        <td class="nunitoR400" style="padding: 16px 0 0 0 ; color:#847662; text-align: right; font-size:14px; font-weight: bold;">
                                            {{ number_format($expediaTotal, 2) }}
                                        </td>
                                        <td class="nunitoR400" style="padding: 16px 0 0 0 ; color:#847662; text-align: right; font-size:14px; font-weight: bold;">
                                            {{ number_format($expediaCommission, 2) }}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                @endif
                @if($expediaCollectsInvoices->isNotEmpty())
                    <div class="card">
                        <div class="section-title">Expedia (Expedia Collects)</div>
                        <div style="width: 100%; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                    border-collapse: collapse; ">
                            <div style="padding: 16px;">
                                <table class="guest-table" style="border-spacing: 0 12px;">
                                    <thead style="margin: 0 0 12px 0;">
                                    <tr>
                                        <th style="color:#847662; width: 4%;  font-size:14px; text-align: left; padding-bottom:12px;">SN</th>
                                        <th style="color:#847662; width: 14%; font-size:14px; text-align: left; padding-bottom:12px;">C/IN</th>
                                        <th style="color:#847662; width: 14%; font-size:14px; text-align: left; padding-bottom:12px;">C/OUT</th>
                                        <th style="color:#847662; width: 22%; font-size:14px; text-align: left; padding-bottom:12px;">GUEST</th>
                                        <th style="color:#847662; width: 23%; font-size:14px; text-align: left; padding-bottom:12px;">ROOM</th>
                                        <th style="color:#847662; width: 12%; font-size:14px; padding-bottom:12px; text-align: right;">TOTAL</th>
                                        <th style="color:#847662; width: 12%; font-size:14px; padding-bottom:12px; text-align: right;">COMM</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($expediaCollectsInvoices   as $invoice)
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
                                                {{ number_format(($invoice['total_amount'] * $invoice['expediaCollectsCommission']) / 100, 2) }}
                                            </td> <!-- Comm -->
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="5" class="nunitoR400" style="padding: 16px 0 0 0 ; color:#847662; text-align: right; font-size:14px; font-weight: bold;">
                                            Total
                                        </td>
                                        <td class="nunitoR400" style="padding: 16px 0 0 0 ; color:#847662; text-align: right; font-size:14px; font-weight: bold;">
                                            {{ number_format($expediaCollectsTotal, 2) }}
                                        </td>
                                        <td class="nunitoR400" style="padding: 16px 0 0 0 ; color:#847662; text-align: right; font-size:14px; font-weight: bold;">
                                            {{ number_format($expediaCollectsCommission, 2) }}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                @endif
                 <div class="card">
                        <div class="section-title">Invoice Summary</div>
                        <div style="width: 100%; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                        border-collapse: collapse; ">
                            <div style="padding: 16px;">
                                <table class="guest-table" style="border-spacing: 0 12px;">
                                    <thead style="margin: 0 0 12px 0;">
                                    <tr>
                                        <th style="color:#847662; width: 4%;  font-size:14px; text-align: left; padding-bottom:12px;">SN</th>
                                        <th style="color:#847662; width: 26%; font-size:14px; text-align: left; padding-bottom:12px;">Description</th>
                                        <th style="color:#847662; width: 15%; font-size:14px; text-align: left; padding-bottom:12px;">Debit / Credit</th>
                                        <th style="color:#847662; width: 15%; font-size:14px;  padding-bottom:12px; text-align: right;">Amount (TK)</th>
                                        <th style="color:#847662; width: 20%; font-size:14px;  padding-bottom:12px; text-align: right;">Total (TK)</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($combinedData as $index => $row)
                                        <tr>
                                            <td class="nunitoR400" style="text-align: left; font-size:14px;">{{ sprintf('%02d',  str_pad($index+1, 2, '0', STR_PAD_LEFT) ) }}</td>
                                            <td class="nunitoR400" style="text-align: left; font-size:14px;">{{ $row['description'] }}</td>
                                            <td class="nunitoR400" style="text-align: left; font-size:14px;">{{ $row['type'] }}</td>
                                            <td class="nunitoR400" style="text-align: right; font-size:14px;">{{ number_format($row['amount'], 2) }}</td>
                                            <td class="nunitoR400" style="text-align: right; font-size:14px;">{{ number_format($row['total'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="4" class="nunitoR400" style="padding: 16px 0 0 0 ; color:red; text-align: right; font-size:14px; ">
                                            Total Payable To Hotel
                                        </td>
                                        <td class="nunitoR400" style="padding: 16px 0 0 0 ; color:red; text-align: right; font-size:14px; ">
                                            {{ number_format($combinedGrandTotal, 2) }}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
            @endif
            <!-- Footer -->
            <div class="card" style="padding: 12px">
                <p style="text-align: center"> &copy; Obeo Limited. All rights reserved.</p>
            </div>
        </div>
    </body>
</html>

