<?php

namespace RajChotaliya\RcPdf\Tests;

use RajChotaliya\RcPdf\Facades\RcPdf;

class RcPdfTest extends TestCase
{
    public function test_it_generates_a_pdf_with_header_body_and_footer(): void
    {
        $output = RcPdf::headerHtml('<div>Header</div>')
            ->footerHtml('<div>Footer - Page {PAGENO}</div>')
            ->html('<p>Body content</p>')
            ->paper('A4', 'portrait')
            ->output();

        $this->assertStringStartsWith('%PDF-', $output);
    }

    public function test_it_renders_blade_views_for_each_band(): void
    {
        $this->app['view']->addLocation(__DIR__.'/views');

        $output = RcPdf::header('test-header', ['title' => 'Invoice'])
            ->footer('test-footer', ['footerText' => 'Confidential'])
            ->view('test-body', ['rows' => [['A', 'B']]])
            ->output();

        $this->assertStringStartsWith('%PDF-', $output);
    }

    public function test_it_supports_a_once_off_header_and_a_pinned_group_footer(): void
    {
        $output = RcPdf::headerHtml('<div>Report Header</div>', once: true)
            ->groupFooterHtml('<div>Totals: 100.00</div>')
            ->footerHtml('<div>Page {PAGENO} of {nbpg}</div>')
            ->html(str_repeat('<p>Row</p>', 5))
            ->paper('A4', 'portrait')
            ->output();

        $this->assertStringStartsWith('%PDF-', $output);
    }
}
