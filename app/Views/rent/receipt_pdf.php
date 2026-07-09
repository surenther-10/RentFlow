<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rent Receipt - <?= esc($payment['receipt_number']) ?></title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.4;
            padding: 20px;
            font-size: 14px;
        }
        .receipt-box {
            border: 1px solid #ddd;
            padding: 30px;
            border-radius: 8px;
            background-color: #fff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 8px 0;
            vertical-align: top;
        }
        .header-table td {
            padding: 0;
        }
        .title {
            font-size: 26px;
            font-weight: bold;
            color: #6366f1;
            margin: 0;
        }
        .invoice-details {
            text-align: right;
            font-size: 13px;
            color: #777;
        }
        .divider {
            border-bottom: 2px solid #6366f1;
            margin: 20px 0;
        }
        .info-table {
            margin-bottom: 30px;
        }
        .info-table td {
            width: 50%;
        }
        .section-title {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            color: #4f46e5;
            margin-bottom: 8px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .details-table {
            width: 100%;
            margin-top: 10px;
        }
        .details-table th {
            background-color: #f3f4f6;
            color: #4b5563;
            text-align: left;
            padding: 10px;
            font-size: 12px;
            text-transform: uppercase;
            border-bottom: 1px solid #e5e7eb;
        }
        .details-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #eee;
        }
        .total-row td {
            font-weight: bold;
            font-size: 16px;
            color: #111827;
            background-color: #fafafa;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 11px;
            color: #9ca3af;
        }
    </style>
</head>
<body>

<div class="receipt-box">
    <!-- Header -->
    <table class="header-table">
        <tr>
            <td>
                <h1 class="title">RentFlow</h1>
                <span style="color: #777; font-size: 12px;">Premium Rental Management Systems</span>
            </td>
            <td class="invoice-details">
                <strong>Receipt No:</strong> <?= esc($payment['receipt_number']) ?><br>
                <strong>Date:</strong> <?= date('d M Y', strtotime($payment['payment_date'])) ?><br>
                <strong>Status:</strong> <span style="color: #16a34a; font-weight: bold;"><?= esc($payment['status']) ?></span>
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    <!-- Parties Involved -->
    <table class="info-table">
        <tr>
            <td>
                <div class="section-title">Received From (Tenant)</div>
                <strong><?= esc($payment['tenant_name']) ?></strong><br>
                Phone: <?= esc($payment['tenant_mobile']) ?><br>
                Email: <?= esc($payment['tenant_email']) ?>
            </td>
            <td>
                <div class="section-title">Property Rented</div>
                <strong><?= esc($payment['property_name']) ?></strong><br>
                <?= esc($lease['property_address']) ?>, <?= esc($lease['city']) ?><br>
                <?= esc($lease['state']) ?> - <?= esc($lease['pincode']) ?>
            </td>
        </tr>
    </table>

    <!-- Ledger table -->
    <table class="details-table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Payment Method</th>
                <th>Period</th>
                <th style="text-align: right;">Amount Paid</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    Monthly Rent Payment<br>
                    <small style="color: #777; font-size: 11px;"><?= esc($payment['notes'] ?: 'No extra notes recorded.') ?></small>
                </td>
                <td><?= esc($payment['payment_method']) ?></td>
                <td><?= date('F Y', strtotime($payment['payment_date'])) ?></td>
                <td style="text-align: right; font-weight: 500;">₹<?= number_format($payment['amount'], 2) ?></td>
            </tr>
            <tr class="total-row">
                <td colspan="3" style="text-align: right;">TOTAL RECEIVED</td>
                <td style="text-align: right; color: #4f46e5;">₹<?= number_format($payment['amount'], 2) ?></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        This is a computer-generated transaction receipt issued on behalf of the Property Owner.<br>
        For any discrepancies, please reach out to admin@rental.com. Thank you!
    </div>
</div>

</body>
</html>
