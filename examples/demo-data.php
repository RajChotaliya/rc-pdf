<?php

/**
 * Shared sample data for the RC PDF examples, modeled after a Crystal
 * Reports style invoice: Page Header (logo/addresses/doc info/column
 * titles), Detail (line items), Group/Report Footer (totals/signature)
 * and Page Footer. 60 items are generated so the Detail band spans
 * several pages and the Page Header/Footer repeat on each one.
 */

function rc_pdf_demo_data(): array
{
    $items = [
        ['code' => 'N-8650', 'description' => 'NOKIA 8650', 'price' => 1900.00, 'uom' => 'UNIT'],
        ['code' => 'N-8250', 'description' => 'NOKIA 8250', 'price' => 890.00, 'uom' => 'UNIT'],
        ['code' => 'N-3310', 'description' => 'NOKIA 3310', 'price' => 320.00, 'uom' => 'UNIT'],
        ['code' => 'N-6110', 'description' => 'NOKIA 6110', 'price' => 650.00, 'uom' => 'UNIT'],
        ['code' => 'N-7110', 'description' => 'NOKIA 7110', 'price' => 780.00, 'uom' => 'UNIT'],
    ];

    $rows = [];
    $gross = 0.0;

    for ($i = 1; $i <= 120; $i++) {
        $item = $items[($i - 1) % count($items)];
        $qty = 2 + ($i % 4);
        $subtotal = $qty * $item['price'];
        $gross += $subtotal;

        $rows[] = [
            'no' => $i,
            'code' => $item['code'],
            'description' => $item['description'],
            'qty' => $qty,
            'uom' => $item['uom'],
            'price' => $item['price'],
            'subtotal' => $subtotal,
        ];
    }

    $discount = 0.0;
    $totalExclTax = $gross - $discount;
    $tax = 0.0;
    $rounding = 0.0;
    $totalPayable = $totalExclTax + $tax + $rounding;

    return [
        'company' => [
            'name' => 'Testing Company - Demo',
            'address' => 'Address is blank',
        ],
        'title' => 'Invoice',
        'billing' => [
            'name' => 'ALPHA & BETA COMPUTER',
            'address' => "838 JALAN WORLD\n40485 RAWANG\nSELANGOR DE",
            'attn' => 'MR ALPHA',
            'tel' => '03-48573689',
            'fax' => '03-48573690',
        ],
        'delivery' => [
            'name' => 'ALPHA & BETA COMPUTER',
            'address' => "838 JALAN WORLD\n40485 RAWANG\nSELANGOR DE",
            'attn' => 'MR ALPHA',
            'tel' => '03-48573689',
            'fax' => '03-48573690',
        ],
        'docInfo' => [
            'account' => '300-A0002',
            'salesExec' => 'NF',
            'docDate' => '20/02/2015',
            'docNo' => 'DO-00006',
            'invoiceNo' => 'IV-00004',
            'invoiceDate' => '22/08/2026',
        ],
        'rows' => $rows,
        'footerText' => 'Testing Company - Current Use',
        'totals' => [
            'amountInWords' => 'NINE THOUSAND THREE HUNDRED AND EIGHTY ONLY',
            'gross' => $gross,
            'discount' => $discount,
            'totalExclTax' => $totalExclTax,
            'tax' => $tax,
            'rounding' => $rounding,
            'totalPayable' => $totalPayable,
            'paymentTerms' => '45 Days',
        ],
    ];
}
