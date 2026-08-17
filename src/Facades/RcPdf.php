<?php

namespace RajChotaliya\RcPdf\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \RajChotaliya\RcPdf\RcPdf view(string $view, array $data = [])
 * @method static \RajChotaliya\RcPdf\RcPdf html(string $html)
 * @method static \RajChotaliya\RcPdf\RcPdf groupFooter(string $view, array $data = [], string $gap = '4mm')
 * @method static \RajChotaliya\RcPdf\RcPdf groupFooterHtml(string $html, string $gap = '4mm')
 * @method static \RajChotaliya\RcPdf\RcPdf header(string $view, array $data = [], bool $once = false)
 * @method static \RajChotaliya\RcPdf\RcPdf headerHtml(string $html, bool $once = false)
 * @method static \RajChotaliya\RcPdf\RcPdf footer(string $view, array $data = [])
 * @method static \RajChotaliya\RcPdf\RcPdf footerHtml(string $html)
 * @method static \RajChotaliya\RcPdf\RcPdf paper(string $format, string $orientation = 'portrait')
 * @method static \RajChotaliya\RcPdf\RcPdf margins(array $margins)
 * @method static \RajChotaliya\RcPdf\RcPdf options(array $options)
 * @method static \Mpdf\Mpdf mpdf()
 * @method static string output()
 * @method static \Illuminate\Http\Response stream(string $filename = 'document.pdf')
 * @method static \Illuminate\Http\Response download(string $filename = 'document.pdf')
 * @method static bool save(string $path, ?string $disk = null)
 *
 * @see \RajChotaliya\RcPdf\RcPdf
 */
class RcPdf extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'rc-pdf';
    }
}
