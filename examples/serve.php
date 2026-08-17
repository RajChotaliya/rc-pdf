<?php

/**
 * Standalone example: serves a multi-page sample invoice PDF in a
 * browser using PHP's built-in web server as a router script (no full
 * Laravel app needed). Demonstrates the Page Header / Detail / Group
 * Footer / Page Footer bands, Crystal Reports style.
 *
 * Run from the package root:
 *   php -S 127.0.0.1:8000 examples/serve.php
 *
 * Then open http://127.0.0.1:8000/ in your browser.
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

$response = $pdf->stream('sample-invoice.pdf');
$response->send();
