{{--
    Group Footer band: totals + signature. Passed to ->groupFooter(), this
    is rendered once, pinned to the bottom of whichever page it ends up on
    (see RcPdf::writeGroupFooter()) — never floating directly under the last
    detail row, and automatically pushed to a fresh page if it wouldn't fit.
--}}
<div style="font-family: sans-serif; font-size: 9px;">
    <table width="100%">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                RINGGIT MALAYSIA: {{ $amountInWords ?? '' }}
            </td>
            <td style="width: 40%; vertical-align: top;">
                <table width="100%" style="font-size: 9px;">
                    <tr>
                        <td>Gross</td>
                        <td style="text-align: right;">{{ number_format($gross ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Discount</td>
                        <td style="text-align: right;">{{ number_format($discount ?? 0, 2) }}</td>
                    </tr>
                    <tr style="border-top: 1px solid #333;">
                        <td>Total Excl. Tax</td>
                        <td style="text-align: right;">{{ number_format($totalExclTax ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Tax Amt</td>
                        <td style="text-align: right;">{{ number_format($tax ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Rounding Adjustment</td>
                        <td style="text-align: right;">{{ number_format($rounding ?? 0, 2) }}</td>
                    </tr>
                    <tr style="border-top: 2px solid #333; font-weight: bold;">
                        <td>Total Payable Incl. Tax</td>
                        <td style="text-align: right;">{{ number_format($totalPayable ?? 0, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div style="margin-top: 12px;">
        <strong>Payment Terms</strong><br>
        {{ $paymentTerms ?? '' }}
    </div>

    <table width="100%" style="margin-top: 30px;">
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 40%; text-align: center; border-top: 1px solid #333; padding-top: 2px;">
                Authorized Signature
            </td>
        </tr>
    </table>
</div>
