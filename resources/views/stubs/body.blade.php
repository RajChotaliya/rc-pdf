{{--
    Detail band: repeating item rows. Column titles live in <thead> so mPDF
    auto-repeats them at the top of every page this table spans (page 1
    included), even though the rich Report Header above only prints once.
--}}
<table width="100%" cellpadding="3" style="font-family: sans-serif; font-size: 8px; border-collapse: collapse;">
    <thead>
        <tr style="background: #f5f5f5;">
            <th style="text-align: left; width: 5%;">No</th>
            <th style="text-align: left; width: 12%;">Item Code</th>
            <th style="text-align: left; width: 33%;">Description</th>
            <th style="text-align: right; width: 10%;">Qty</th>
            <th style="text-align: left; width: 10%;">UOM</th>
            <th style="text-align: right; width: 15%;">Price</th>
            <th style="text-align: right; width: 15%;">Sub Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows ?? [] as $row)
            <tr>
                <td style="width: 5%;">{{ $row['no'] }}</td>
                <td style="width: 12%;">{{ $row['code'] }}</td>
                <td style="width: 33%;">{{ $row['description'] }}</td>
                <td style="width: 10%; text-align: right;">{{ number_format($row['qty'], 3) }}</td>
                <td style="width: 10%;">{{ $row['uom'] }}</td>
                <td style="width: 15%; text-align: right;">{{ number_format($row['price'], 2) }}</td>
                <td style="width: 15%; text-align: right;">{{ number_format($row['subtotal'], 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
