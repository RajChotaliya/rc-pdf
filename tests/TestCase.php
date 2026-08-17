<?php

namespace RajChotaliya\RcPdf\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use RajChotaliya\RcPdf\RcPdfServiceProvider;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            RcPdfServiceProvider::class,
        ];
    }
}
