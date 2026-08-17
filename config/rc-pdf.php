<?php

return [
    // Default paper size and orientation, passed straight to mPDF.
    'format' => env('RC_PDF_FORMAT', 'A4'),
    'orientation' => env('RC_PDF_ORIENTATION', 'P'), // P = portrait, L = landscape

    // Page margins in millimeters. 'top'/'bottom' should comfortably
    // fit your header/footer heights or they will overlap the body.
    'margins' => [
        'top' => 35,
        'bottom' => 25,
        'left' => 15,
        'right' => 15,
        'header' => 8,
        'footer' => 8,
    ],

    // Default font settings.
    'default_font_size' => 12,
    'default_font' => 'dejavusans',

    // Where generated PDFs are stored when using ->save() with a relative path.
    'disk' => env('RC_PDF_DISK', 'local'),
    'storage_path' => 'rc-pdf',

    // Raw mPDF constructor config, merged with the values above.
    // See: https://mpdf.github.io/reference/mpdf-functions/construct.html
    'mpdf_options' => [
        'tempDir' => storage_path('app/rc-pdf/tmp'),
    ],
];
