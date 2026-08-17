{{-- Page Footer band: rendered on every page, below the body. --}}
<table width="100%" style="border-top: 1px solid #c0392b; font-family: sans-serif; font-size: 8px; color: #555; padding-top: 2px;">
    <tr>
        <td style="width: 70%;">{{ $footerText ?? '' }}</td>
        <td style="width: 30%; text-align: right;">Page {PAGENO} of {nbpg}</td>
    </tr>
</table>
