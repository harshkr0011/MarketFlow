<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $invoice['invoice_no'] }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.4;
            padding: 20px;
            margin: 0;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            padding: 30px;
            border-radius: 8px;
        }
        .header {
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
        }
        .header td {
            vertical-align: top;
        }
        .company-logo {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 1px;
        }
        .company-sub {
            font-size: 11px;
            color: #64748b;
        }
        .invoice-title {
            font-size: 26px;
            font-weight: 900;
            color: #4f46e5;
            text-align: right;
        }
        .invoice-no {
            font-size: 12px;
            color: #475569;
            text-align: right;
        }
        .details-table {
            width: 100%;
            margin-bottom: 30px;
        }
        .details-table td {
            font-size: 12px;
            vertical-align: top;
        }
        .billed-to {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            color: #64748b;
            margin-bottom: 5px;
        }
        .meta-info {
            text-align: right;
            color: #475569;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background-color: #f8fafc;
            border-bottom: 1px solid #cbd5e1;
            padding: 10px;
            font-size: 11px;
            text-transform: uppercase;
            color: #475569;
            text-align: left;
        }
        .items-table td {
            padding: 12px 10px;
            font-size: 12px;
            border-bottom: 1px solid #f1f5f9;
        }
        .items-table .text-right {
            text-align: right;
        }
        .totals-section {
            width: 100%;
        }
        .totals-table {
            width: 60%;
            margin-left: auto;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 6px 10px;
            font-size: 12px;
            color: #475569;
        }
        .totals-table .total-row td {
            font-weight: bold;
            color: #0f172a;
            font-size: 14px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
        .totals-table .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <table>
                <tr>
                    <td>
                        <div class="company-logo">MARKETFLOW PVT. LTD.</div>
                        <div class="company-sub">Level 4, Prestige Tech Park, Outer Ring Road</div>
                        <div class="company-sub">Bengaluru, KA, India - 560103</div>
                        <div class="company-sub">GSTIN: 29AAFCM1234F1Z5 | support@marketflow.in</div>
                    </td>
                    <td>
                        <div class="invoice-title">TAX INVOICE</div>
                        <div class="invoice-no">{{ $invoice['invoice_no'] }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <table class="details-table">
            <tr>
                <td style="width: 50%;">
                    <div class="billed-to">Billed To:</div>
                    <strong>{{ $invoice['user_name'] }}</strong><br>
                    {{ $invoice['user_email'] }}
                </td>
                <td style="width: 50%;" class="meta-info">
                    <div class="billed-to" style="text-align: right;">Transaction Meta:</div>
                    <strong>Date:</strong> {{ $invoice['date'] }}<br>
                    <strong>Payment Method:</strong> {{ $invoice['payment_method'] }}<br>
                    @if(!empty($invoice['upi_id']))
                        <strong>UPI ID:</strong> {{ $invoice['upi_id'] }}<br>
                    @endif
                    <strong>TXN ID:</strong> {{ $invoice['transaction_id'] }}<br>
                    <strong>Status:</strong> <span style="color: #10b981; font-weight: bold;">PAID</span>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th class="text-right" style="width: 35%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $invoice['product_name'] }}</td>
                    <td class="text-right">₹{{ number_format($invoice['price'], 2) }} <span style="color: #64748b; font-size: 10px;">(${{ number_format($invoice['usd_price'], 2) }} USD)</span></td>
                </tr>
            </tbody>
        </table>

        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">₹{{ number_format($invoice['price'], 2) }} <span style="color: #64748b; font-size: 10px;">(${{ number_format($invoice['usd_price'], 2) }} USD)</span></td>
                </tr>
                <tr>
                    <td>GST (18%):</td>
                    <td class="text-right">₹{{ number_format($invoice['tax'], 2) }} <span style="color: #64748b; font-size: 10px;">(${{ number_format($invoice['usd_tax'], 2) }} USD)</span></td>
                </tr>
                <tr class="total-row">
                    <td>Total Paid:</td>
                    <td class="text-right" style="color: #4f46e5;">₹{{ number_format($invoice['total'], 2) }} <span style="color: #4f46e5; font-size: 11px; font-weight: bold;">(${{ number_format($invoice['usd_total'], 2) }} USD)</span></td>
                </tr>
            </table>
        </div>

        <div class="footer">
            Thank you for your business. This is an automatically generated system tax invoice. No signature required.
        </div>
    </div>
</body>
</html>
