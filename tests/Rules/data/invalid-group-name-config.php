<?php

declare(strict_types=1);

use Tests\TestCase;

uses(TestCase::class)->group('');
uses(TestCase::class)->group('  ');
uses(TestCase::class)->group();
pest()->group('');
pest()->group('  ');
pest()->group();
uses(TestCase::class)->group('feature');
pest()->group('integration');
pest()->extend(TestCase::class)->group('browser');
