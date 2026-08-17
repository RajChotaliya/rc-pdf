# RC PDF

Laravel PDF generation package modeled after Crystal Reports: define a **Page Header**, a **Page Body** (the repeating detail section), a **Group Footer** (totals/signature, printed once and pinned to the bottom of the page it lands on) and a **Page Footer**. RC PDF renders the header/footer on every page while the body content flows continuously across as many pages as needed. Built on [mPDF](https://mpdf.github.io/), which has native support for repeating HTML headers/footers.

## Installation

```bash
composer require rajchotaliya/rc-pdf
```

The service provider and `RcPdf` facade are auto-discovered.

Publish the config (optional):

```bash
php artisan vendor:publish --tag=rc-pdf-config
```

Publish the example header/body/footer views (optional):

```bash
php artisan vendor:publish --tag=rc-pdf-views
```

## Usage

```php
use RajChotaliya\RcPdf\Facades\RcPdf;

$pdf = RcPdf::header('reports.invoice.header', ['title' => 'Invoice #1029'])
    ->view('reports.invoice.body', ['rows' => $items])
    ->footer('reports.invoice.footer', ['footerText' => 'Confidential'])
    ->paper('A4', 'portrait')
    ->margins(['top' => 40, 'bottom' => 25]);

return $pdf->download('invoice-1029.pdf');
```

Other output options:

```php
$pdf->stream('invoice.pdf');   // inline in the browser
$pdf->save('invoices/1029.pdf'); // save to a Laravel Storage disk
$pdf->output();                // raw PDF string
```

You can also skip Blade views and pass raw HTML directly:

```php
RcPdf::headerHtml('<h1>Header</h1>')
    ->html('<p>Body</p>')
    ->footerHtml('<div>Page {PAGENO} of {nbpg}</div>')
    ->download();
```

### Page bands

| Method | Crystal Reports equivalent | Renders |
|---|---|---|
| `header()` / `headerHtml()` | Page Header | Repeats on every page, above the body |
| `header($view, $data, once: true)` | Report Header | Once, at the top of page 1 only |
| `view()` / `html()` | Details / Page Body | Flows across pages between header and footer |
| `groupFooter()` / `groupFooterHtml()` | Group Footer | Once, pinned to the bottom of the page it falls on |
| `footer()` / `footerHtml()` | Page Footer | Repeats on every page, below the body |

mPDF footer placeholders like `{PAGENO}` (current page) and `{nbpg}` (total pages) work out of the box inside `footer()`/`footerHtml()` views — handy for a Crystal-Reports-style "Page X of Y". Note: those placeholders only get substituted inside a *repeating* header/footer (registered with mPDF as such) — a `once: true` header is just plain body content, so `{PAGENO}`/`{nbpg}` are printed literally there, not replaced.

#### Report Header (first page only)

For a rich header (logo, addresses, doc info) that should print once at the top of page 1 rather than repeat on every page:

```php
$pdf->header('invoice.header', $data, once: true)
    ->view('invoice.body', $data)
    ->footer('invoice.footer', $data) // still repeats on every page
    ->margins(['top' => 15, 'header' => 0]); // no need to reserve header space anymore
```

If you still want a simple repeating element on continuation pages (e.g. column titles for a long item table), put it in a `<thead>` inside your body's table — mPDF automatically repeats `<thead>` rows at the top of every page a table spans (see [resources/views/stubs/body.blade.php](resources/views/stubs/body.blade.php)).

### Group footer (pinned to the bottom of the page, last page only)

A Crystal Reports **Group Footer** (totals, signature block, etc.) is different from the Page Footer: it's not repeated on every page — it prints once, right after the last detail row — but it should still sit flush against the bottom of whichever page it lands on, not float directly under the last row, and never eat into the space of earlier pages.

```php
$pdf->view('invoice.body', ['rows' => $items])
    ->groupFooter('invoice.group-footer', $totals);
```

Under the hood (`RcPdf::writeGroupFooter()`), after the body finishes writing:

1. It measures the group footer's rendered height with mPDF's own layout engine.
2. If it fits in the space remaining on the current page (above the Page Footer), it's pinned there via `position: absolute` — no reserved blank space needed on earlier pages, so the Detail rows keep flowing continuously right up to wherever they naturally end.
3. If it doesn't fit, a fresh page is started first (`<pagebreak margin-bottom="...">`, sized to fit it), and it's pinned to the bottom of that new page instead — it never overlaps the last few rows or the Page Footer.

You don't need to reserve extra bottom margin yourself — just keep `margins(['bottom' => ...])` sized for the Page Footer alone (e.g. `20`).

### Paper & margins

```php
$pdf->paper('A4', 'landscape');
$pdf->margins([
    'top' => 40,     // must be >= header height
    'bottom' => 30,  // must be >= footer height
    'left' => 15,
    'right' => 15,
    'header' => 8,   // gap between header and body
    'footer' => 8,   // gap between body and footer
]);
```

Defaults live in `config/rc-pdf.php` and can be overridden per-PDF via `paper()`/`margins()`, or globally via `.env` (`RC_PDF_FORMAT`, `RC_PDF_ORIENTATION`).

### Advanced mPDF options

Any raw [mPDF constructor option](https://mpdf.github.io/reference/mpdf-functions/construct.html) can be passed through:

```php
$pdf->options(['mode' => 'utf-8', 'tempDir' => storage_path('app/rc-pdf/tmp')]);

// Need direct access to the underlying Mpdf instance (e.g. to add a watermark)?
$mpdf = $pdf->mpdf();
$mpdf->SetWatermarkText('DRAFT');
$mpdf->showWatermarkText = true;
```

## Testing

```bash
composer install
vendor/bin/phpunit
```
