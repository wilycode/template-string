<?php

declare(strict_types=1);

namespace WilyCode\Template\Tests\Util;

use ArrayObject;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use WilyCode\Template\Util\StringUtils;

final class StringUtilsTest extends TestCase
{
    private string $previousTz;

    protected function setUp(): void
    {
        parent::setUp();

        $this->previousTz = date_default_timezone_get();
        date_default_timezone_set('UTC');
    }

    protected function tearDown(): void
    {
        date_default_timezone_set($this->previousTz);

        parent::tearDown();
    }

    public function testUpper(): void
    {
        self::assertSame('ABC', StringUtils::upper('abc'));
        self::assertSame('ŻÓŁĆ', StringUtils::upper('żółć'));
    }

    public function testLower(): void
    {
        self::assertSame('abc', StringUtils::lower('ABC'));
        self::assertSame('żółć', StringUtils::lower('ŻÓŁĆ'));
    }

    public function testUcfirst(): void
    {
        self::assertSame('Abc', StringUtils::ucfirst('abc'));
        self::assertSame('Żółć', StringUtils::ucfirst('żółć'));
        self::assertSame('', StringUtils::ucfirst(''));
    }

    public function testTrimVariants(): void
    {
        self::assertSame('abc', StringUtils::trim('  abc  '));
        self::assertSame('abc  ', StringUtils::ltrim('  abc  '));
        self::assertSame('  abc', StringUtils::rtrim('  abc  '));

        self::assertSame('abc', StringUtils::trim('...abc...', '.'));
    }

    public function testLengthForStringArrayAndCountable(): void
    {
        self::assertSame(4, StringUtils::length('Żółć'));
        self::assertSame(3, StringUtils::length(['a', 'b', 'c']));

        $countable = new ArrayObject([1, 2, 3, 4]);
        self::assertSame(4, StringUtils::length($countable));
    }

    public function testTruncateReturnsOriginalWhenNotNeeded(): void
    {
        self::assertSame('Hello', StringUtils::truncate('Hello', 10));
        self::assertSame('Hello', StringUtils::truncate('Hello', 5));
    }

    public function testTruncateTruncatesAndAppendsSuffix(): void
    {
        self::assertSame('Hello...', StringUtils::truncate('Hello world', 5));
        self::assertSame('Hello (more)', StringUtils::truncate('Hello world', 5, ' (more)'));
    }

    public function testTruncateWithNonPositiveLengthReturnsEmptyString(): void
    {
        self::assertSame('', StringUtils::truncate('Hello world', 0));
        self::assertSame('', StringUtils::truncate('Hello world', -1));
    }

    public function testReplace(): void
    {
        self::assertSame('a-b-c', StringUtils::replace('a b c', ' ', '-'));
        self::assertSame('foobar', StringUtils::replace('fooBAR', 'BAR', 'bar'));
    }

    public function testRegexReplace(): void
    {
        self::assertSame('a-b-c', StringUtils::regexReplace('a  b   c', '/\s+/', '-'));
        self::assertSame('123', StringUtils::regexReplace('a1b2c3', '/\D+/', ''));
    }

    public function testSlug(): void
    {
        self::assertSame('hello-world', StringUtils::slug('Hello, World!'));
        self::assertSame('a-b', StringUtils::slug('  A   B  '));
        self::assertSame('hello_world', StringUtils::slug('Hello World', '_'));
        self::assertSame('zażółć-gęślą-jaźń', StringUtils::slug('Zażółć gęślą jaźń'));
        self::assertSame('café-crème', StringUtils::slug('Café Crème'));
        self::assertSame('hello.world', StringUtils::slug(' Hello, World! ', '.'));
        self::assertSame('a--b', StringUtils::slug('A  B', '--'));
        self::assertSame('', StringUtils::slug('! ... !'));
    }

    public function testFormatDateFromDateTimeInterface(): void
    {
        $dt = new DateTimeImmutable('2026-02-03 10:15:00', new DateTimeZone('UTC'));

        self::assertSame('2026-02-03 10:15', StringUtils::formatDate($dt, 'Y-m-d H:i'));
    }

    public function testFormatDateConvertsTimezoneWhenProvided(): void
    {
        $dt = new DateTimeImmutable('2026-02-03 10:15:00', new DateTimeZone('UTC'));

        self::assertSame(
            '2026-02-03 11:15',
            StringUtils::formatDate($dt, 'Y-m-d H:i', new DateTimeZone('Europe/Warsaw'))
        );
    }

    public function testFormatDatePreservesMicrosecondsWhenChangingTimezone(): void
    {
        $dt = new DateTimeImmutable('2026-02-03 10:15:00.123456', new DateTimeZone('UTC'));

        self::assertSame(
            '2026-02-03 11:15:00.123456',
            StringUtils::formatDate($dt, 'Y-m-d H:i:s.u', new DateTimeZone('Europe/Warsaw'))
        );
    }

    public function testFormatDateFromUnixTimestamp(): void
    {
        $dt = new DateTimeImmutable('2026-02-03 10:15:00', new DateTimeZone('UTC'));
        $ts = $dt->getTimestamp();

        self::assertSame(
            '2026-02-03 10:15',
            StringUtils::formatDate($ts, 'Y-m-d H:i', new DateTimeZone('UTC'))
        );
    }

    public function testFormatDateFromString(): void
    {
        self::assertSame(
            '2026-02-03 10:15',
            StringUtils::formatDate('2026-02-03 10:15:00', 'Y-m-d H:i', new DateTimeZone('UTC'))
        );
    }

    public function testFormatDateReturnsOriginalWhenStringIsNotParsable(): void
    {
        self::assertSame(
            'not-a-date',
            StringUtils::formatDate('not-a-date', 'Y-m-d', new DateTimeZone('UTC'))
        );
    }

    public function testFormatPrice(): void
    {
        self::assertSame('1 234,50', StringUtils::formatPrice(1234.5));
        self::assertSame('1,00', StringUtils::formatPrice(1, 2, ',', ' '));
        self::assertSame('0,00', StringUtils::formatPrice('not-numeric'));
    }
}
