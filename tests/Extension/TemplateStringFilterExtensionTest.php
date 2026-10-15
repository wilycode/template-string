<?php

declare(strict_types=1);

namespace WilyCode\Template\Tests\Extension;

use PHPUnit\Framework\TestCase;
use WilyCode\Template\Extension\TemplateStringFilterExtension;
use WilyCode\Template\FileLocator;
use WilyCode\Template\TemplateEngine;
use WilyCode\Template\TemplateLoader;

final class TemplateStringFilterExtensionTest extends TestCase
{
    private function makeEngine(): TemplateEngine
    {
        return new TemplateEngine(new TemplateLoader(new FileLocator()), sys_get_temp_dir());
    }

    public function testRegisterAddsAllExpectedFilters(): void
    {
        $engine = $this->makeEngine();
        $engine->addExtension(new TemplateStringFilterExtension());

        self::assertSame('ŻÓŁĆ', $engine->applyFilter('upper', 'żółć'));
        self::assertSame('żółć', $engine->applyFilter('lower', 'ŻÓŁĆ'));
        self::assertSame('Żółć', $engine->applyFilter('ucfirst', 'żółć'));
        self::assertSame('abc', $engine->applyFilter('trim', '  abc  '));
        self::assertSame('abc  ', $engine->applyFilter('ltrim', '  abc  '));
        self::assertSame('  abc', $engine->applyFilter('rtrim', '  abc  '));
        self::assertSame(4, $engine->applyFilter('length', 'Żółć'));
        self::assertSame('Hello...', $engine->applyFilter('truncate', 'Hello world', 5));
        self::assertSame('a-b', $engine->applyFilter('replace', 'a b', ' ', '-'));
        self::assertSame('a-b', $engine->applyFilter('regex_replace', 'a  b', '/\s+/', '-'));
        self::assertSame('zażółć-gęślą-jaźń', $engine->applyFilter('slug', 'Zażółć gęślą jaźń'));
        self::assertSame('2026-02-03', $engine->applyFilter('date', '2026-02-03 10:15:00', 'Y-m-d'));
        self::assertSame('1 234,50', $engine->applyFilter('price', 1234.5));
    }

    public function testCustomFilterDefaultsAndOverrides(): void
    {
        $engine = $this->makeEngine();
        $engine->addExtension(new TemplateStringFilterExtension(
            defaultTruncateLength: 4,
            defaultTruncateSuffix: '…',
            defaultPriceDecimals: 0,
            defaultPriceDecimalSeparator: '.',
            defaultPriceThousandsSeparator: ',',
        ));

        self::assertSame('Hell…', $engine->applyFilter('truncate', 'Hello world'));
        self::assertSame('Hello (more)', $engine->applyFilter('truncate', 'Hello world', 5, ' (more)'));
        self::assertSame('1,235', $engine->applyFilter('price', 1234.5));
        self::assertSame('1 234,50', $engine->applyFilter('price', 1234.5, 2, ',', ' '));
    }
}
