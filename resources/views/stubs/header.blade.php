{{--
    Report Header band: rendered ONCE, at the top of the first page only
    (pass `once: true` to ->header()/->headerHtml()). Because it's plain
    body content rather than an mPDF page header, {PAGENO}/{nbpg}
    placeholders are NOT replaced here — use the Page Footer for those.
--}}
<table width="100%" style="font-family: sans-serif; border-bottom: 2px solid #c0392b; padding-bottom: 4px;">
    <tr>
        <td style="width: 55px; vertical-align: top;">
            <div style="width: 46px; height: 46px; border-radius: 50%; background: #e67e22; color: #fff; text-align: center; line-height: 46px; font-size: 20px; font-weight: bold;">
                {{ strtoupper(substr($company['name'] ?? 'C', 0, 1)) }}
            </div>
        </td>
        <td style="vertical-align: top;">
            <div style="font-size: 15px; font-weight: bold;">{{ $company['name'] ?? 'Company Name' }}</div>
            <div style="font-size: 9px; color: #555;">{{ $company['address'] ?? 'Address is blank' }}</div>
        </td>
    </tr>
</table>

<div style="font-family: sans-serif; font-size: 18px; font-weight: bold; margin: 8px 0 4px;">
    {{ $title ?? 'Invoice' }}
</div>

<table width="100%" style="font-family: sans-serif; font-size: 9px; border-top: 1px solid #ccc; border-bottom: 1px solid #ccc;">
    <tr>
        <td style="width: 50%; vertical-align: top; padding: 4px 0;">
            <strong>Billing Address</strong><br>
            <strong>{{ $billing['name'] ?? '' }}</strong><br>
            {{ $billing['address'] ?? '' }}<br><br>
            Attn: {{ $billing['attn'] ?? '' }}<br>
            Tel: {{ $billing['tel'] ?? '' }}<br>
            Fax: {{ $billing['fax'] ?? '' }}
        </td>
        <td style="width: 50%; vertical-align: top; padding: 4px 0;">
            <strong>Delivery Address</strong><br>
            <strong>{{ $delivery['name'] ?? '' }}</strong><br>
            {{ $delivery['address'] ?? '' }}<br><br>
            Attn: {{ $delivery['attn'] ?? '' }}<br>
            Tel: {{ $delivery['tel'] ?? '' }}<br>
            Fax: {{ $delivery['fax'] ?? '' }}
        </td>
    </tr>
</table>

<table width="100%" cellpadding="3" style="font-family: sans-serif; font-size: 8px; border-collapse: collapse; margin-top: 4px;">
    <tr style="background: #eee;">
        <th style="border: 1px solid #ccc; text-align: left;">Customer Account</th>
        <th style="border: 1px solid #ccc; text-align: left;">Sales Executive</th>
        <th style="border: 1px solid #ccc; text-align: left;">Doc Date</th>
        <th style="border: 1px solid #ccc; text-align: left;">Doc No.</th>
        <th style="border: 1px solid #ccc; text-align: left;">Invoice No.</th>
        <th style="border: 1px solid #ccc; text-align: left;">Invoice Date</th>
    </tr>
    <tr>
        <td style="border: 1px solid #ccc;">{{ $docInfo['account'] ?? '' }}</td>
        <td style="border: 1px solid #ccc;">{{ $docInfo['salesExec'] ?? '' }}</td>
        <td style="border: 1px solid #ccc;">{{ $docInfo['docDate'] ?? '' }}</td>
        <td style="border: 1px solid #ccc;">{{ $docInfo['docNo'] ?? '' }}</td>
        <td style="border: 1px solid #ccc;">{{ $docInfo['invoiceNo'] ?? '' }}</td>
        <td style="border: 1px solid #ccc;">{{ $docInfo['invoiceDate'] ?? '' }}</td>
    </tr>
</table>
