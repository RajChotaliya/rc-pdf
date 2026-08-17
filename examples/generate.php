<?php

/**
 * Standalone example: boots a minimal Laravel app (via Testbench) just
 * enough to resolve the RcPdf facade, and writes a real multi-page PDF
 * to disk so you can open it and see the Page Header / Detail / Group
 * Footer / Page Footer bands rendered (Crystal Reports style layout).
 *
 * Run from the package root:
 *   php examples/generate.php
 */

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/demo-data.php';

use Orchestra\Testbench\Foundation\Application;
use RajChotaliya\RcPdf\RcPdfServiceProvider;

$app = Application::create(
    basePath: __DIR__.'/../vendor/orchestra/testbench-core/laravel',
    options: [
        'extra' => [
            'providers' => [RcPdfServiceProvider::class],
        ],
    ],
);

$viewFactory = $app->make('view');
$viewFactory->addLocation(__DIR__.'/../resources/views/stubs');

$data = rc_pdf_demo_data();

$pdf = $app->make('rc-pdf');

$pdf->header('header', [
    'company' => $data['company'],
    'title' => $data['title'],
    'billing' => $data['billing'],
    'delivery' => $data['delivery'],
    'docInfo' => $data['docInfo'],
], once: true) // Report Header: prints once, top of page 1 only
    ->view('body', [
        'rows' => $data['rows'],
    ])
    ->groupFooter('group-footer', $data['totals']) // pinned to bottom, last page only
    ->footer('footer', [
        'footerText' => $data['footerText'],
    ])
    ->paper('A4', 'portrait')
    ->margins([
        'top' => 15,
        'bottom' => 20,
        'header' => 0,
        'footer' => 4,
    ]);

$outputPath = __DIR__.'/output.pdf';
file_put_contents($outputPath, $pdf->output());

echo "PDF written to: {$outputPath}\n";
