<?php

namespace RajChotaliya\RcPdf;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class RcPdf
{
    protected ViewFactory $viewFactory;

    protected array $config;

    protected ?string $headerView = null;
    protected array $headerData = [];
    protected ?string $headerHtml = null;
    protected bool $headerOnce = false;

    protected ?string $footerView = null;
    protected array $footerData = [];
    protected ?string $footerHtml = null;

    protected ?string $bodyView = null;
    protected array $bodyData = [];
    protected ?string $bodyHtml = null;

    protected ?string $groupFooterView = null;
    protected array $groupFooterData = [];
    protected ?string $groupFooterHtml = null;
    protected string $groupFooterGap = '4mm';

    protected string $format;
    protected string $orientation;
    protected array $margins;

    protected array $mpdfOptions = [];

    protected ?Mpdf $mpdf = null;

    public function __construct(ViewFactory $viewFactory, array $config)
    {
        $this->viewFactory = $viewFactory;
        $this->config = $config;

        $this->format = $config['format'] ?? 'A4';
        $this->orientation = $config['orientation'] ?? 'P';
        $this->margins = $config['margins'] ?? [];
        $this->mpdfOptions = $config['mpdf_options'] ?? [];
    }

    /**
     * Set the page body (the repeating "detail" section) from a Blade view.
     */
    public function view(string $view, array $data = []): static
    {
        $this->bodyView = $view;
        $this->bodyData = $data;
        $this->bodyHtml = null;

        return $this;
    }

    /**
     * Set the page body directly from raw HTML.
     */
    public function html(string $html): static
    {
        $this->bodyHtml = $html;
        $this->bodyView = null;

        return $this;
    }

    /**
     * Set the Group Footer (totals, signature block, etc.) from a Blade view.
     * Renders once, right after the last body row, pinned to the bottom of
     * whichever page it lands on — like a Crystal Report Group Footer band.
     * If it wouldn't fit in the remaining space on the current page, it's
     * automatically pushed onto a fresh page instead of overlapping content.
     */
    public function groupFooter(string $view, array $data = [], string $gap = '4mm'): static
    {
        $this->groupFooterView = $view;
        $this->groupFooterData = $data;
        $this->groupFooterHtml = null;
        $this->groupFooterGap = $gap;

        return $this;
    }

    /**
     * Set the Group Footer directly from raw HTML. See groupFooter().
     */
    public function groupFooterHtml(string $html, string $gap = '4mm'): static
    {
        $this->groupFooterHtml = $html;
        $this->groupFooterView = null;
        $this->groupFooterGap = $gap;

        return $this;
    }

    /**
     * Set the page header. By default it repeats on every page (like a Crystal
     * Report Page Header band). Pass $once = true to render it only once, at
     * the top of the first page (like a Crystal Report Report Header band).
     */
    public function header(string $view, array $data = [], bool $once = false): static
    {
        $this->headerView = $view;
        $this->headerData = $data;
        $this->headerHtml = null;
        $this->headerOnce = $once;

        return $this;
    }

    /**
     * Set the page header directly from raw HTML. Pass $once = true to render
     * it only once, at the top of the first page, instead of on every page.
     */
    public function headerHtml(string $html, bool $once = false): static
    {
        $this->headerHtml = $html;
        $this->headerView = null;
        $this->headerOnce = $once;

        return $this;
    }

    /**
     * Set the page footer, rendered on every page (like a Crystal Report Page Footer band).
     */
    public function footer(string $view, array $data = []): static
    {
        $this->footerView = $view;
        $this->footerData = $data;
        $this->footerHtml = null;

        return $this;
    }

    /**
     * Set the page footer directly from raw HTML.
     */
    public function footerHtml(string $html): static
    {
        $this->footerHtml = $html;
        $this->footerView = null;

        return $this;
    }

    /**
     * Configure paper size and orientation. Orientation accepts 'portrait'/'landscape' or 'P'/'L'.
     */
    public function paper(string $format, string $orientation = 'portrait'): static
    {
        $this->format = $format;
        $this->orientation = str_starts_with(strtolower($orientation), 'l') ? 'L' : 'P';

        return $this;
    }

    /**
     * Override page margins (millimeters). Any keys not passed keep their configured default.
     */
    public function margins(array $margins): static
    {
        $this->margins = array_merge($this->margins, $margins);

        return $this;
    }

    /**
     * Merge/override raw mPDF constructor options.
     */
    public function options(array $options): static
    {
        $this->mpdfOptions = array_merge($this->mpdfOptions, $options);

        return $this;
    }

    protected function renderHeader(): ?string
    {
        if ($this->headerHtml !== null) {
            return $this->headerHtml;
        }

        if ($this->headerView !== null) {
            return $this->viewFactory->make($this->headerView, $this->headerData)->render();
        }

        return null;
    }

    protected function renderFooter(): ?string
    {
        if ($this->footerHtml !== null) {
            return $this->footerHtml;
        }

        if ($this->footerView !== null) {
            return $this->viewFactory->make($this->footerView, $this->footerData)->render();
        }

        return null;
    }

    protected function renderBody(): string
    {
        if ($this->bodyHtml !== null) {
            return $this->bodyHtml;
        }

        if ($this->bodyView !== null) {
            return $this->viewFactory->make($this->bodyView, $this->bodyData)->render();
        }

        return '';
    }

    protected function renderGroupFooter(): ?string
    {
        if ($this->groupFooterHtml !== null) {
            return $this->groupFooterHtml;
        }

        if ($this->groupFooterView !== null) {
            return $this->viewFactory->make($this->groupFooterView, $this->groupFooterData)->render();
        }

        return null;
    }

    /**
     * Build (or rebuild) the underlying Mpdf instance with header/footer/body applied.
     */
    public function mpdf(): Mpdf
    {
        $mpdfConfig = array_merge($this->mpdfOptions, [
            'format' => $this->format,
            'orientation' => $this->orientation,
            'margin_top' => $this->margins['top'] ?? 35,
            'margin_bottom' => $this->margins['bottom'] ?? 25,
            'margin_left' => $this->margins['left'] ?? 15,
            'margin_right' => $this->margins['right'] ?? 15,
            'margin_header' => $this->margins['header'] ?? 8,
            'margin_footer' => $this->margins['footer'] ?? 8,
            'default_font_size' => $this->config['default_font_size'] ?? 12,
            'default_font' => $this->config['default_font'] ?? 'dejavusans',
        ]);

        $tempDir = $mpdfConfig['tempDir'] ?? null;
        if ($tempDir && ! is_dir($tempDir)) {
            @mkdir($tempDir, 0775, true);
        }

        $mpdf = new Mpdf($mpdfConfig);

        $header = $this->renderHeader();

        if ($header && ! $this->headerOnce) {
            $mpdf->SetHTMLHeader($header);
        }

        if ($footer = $this->renderFooter()) {
            $mpdf->SetHTMLFooter($footer);
        }

        $body = $this->renderBody();

        if ($header && $this->headerOnce) {
            $body = $header.$body;
        }

        $mpdf->WriteHTML($body);

        if ($groupFooter = $this->renderGroupFooter()) {
            $this->writeGroupFooter($mpdf, $groupFooter);
        }

        $this->mpdf = $mpdf;

        return $mpdf;
    }

    /**
     * Write the Group Footer pinned to the true bottom of the current page,
     * unless it doesn't fit in what's actually left there — in which case a
     * fresh page is started with just enough bottom margin reserved to fit
     * it, so it never overlaps the preceding body content.
     *
     * The footer is drawn as position:absolute measured from the page's
     * physical bottom edge, not from the configured bMargin band — so "does
     * it fit" has to be checked against the real leftover space to that
     * edge ($mpdf->h - $mpdf->y), not against bMargin itself. That keeps
     * the document's bottom margin free to stay small for normal content
     * flow (more rows per page) instead of being permanently inflated on
     * every page just to guarantee room for a footer that only ever lands
     * on the last one.
     */
    protected function writeGroupFooter(Mpdf $mpdf, string $html): void
    {
        $leftMargin = (float) ($this->margins['left'] ?? 15);
        $rightMargin = (float) ($this->margins['right'] ?? 15);
        $contentWidth = $mpdf->w - $leftMargin - $rightMargin;
        $gapMm = $this->toMillimeters($this->groupFooterGap);

        $height = $mpdf->_getHtmlHeight($html);
        $remaining = $mpdf->h - $mpdf->y;
        $bottomOffset = $gapMm;

        if ($height + $gapMm > $remaining) {
            $needed = $height + $gapMm;
            $mpdf->WriteHTML('<pagebreak margin-bottom="'.$needed.'mm" />');
        }

        $mpdf->WriteHTML(sprintf(
            '<div style="position: absolute; left: %smm; width: %smm; bottom: %smm;">%s</div>',
            $leftMargin,
            $contentWidth,
            $bottomOffset,
            $html
        ));
    }

    protected function toMillimeters(string $value): float
    {
        return (float) preg_replace('/[^0-9.\-]/', '', $value);
    }

    /**
     * Return the generated PDF as a raw string.
     */
    public function output(): string
    {
        return $this->mpdf()->Output('', Destination::STRING_RETURN);
    }

    /**
     * Stream the PDF to the browser inline.
     */
    public function stream(string $filename = 'document.pdf')
    {
        $content = $this->output();

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }

    /**
     * Force-download the PDF to the browser.
     */
    public function download(string $filename = 'document.pdf')
    {
        $content = $this->output();

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * Save the PDF to a given disk (defaults to config('rc-pdf.disk')).
     */
    public function save(string $path, ?string $disk = null): bool
    {
        $disk ??= $this->config['disk'] ?? 'local';

        return Storage::disk($disk)->put($path, $this->output());
    }
}
