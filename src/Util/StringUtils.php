<?php

declare(strict_types=1);

namespace WilyCode\Template\Util;

use Countable;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;

/**
 * Utility helpers used by template filters.
 *
 * Keeping the logic here makes Template*Extension classes thin and easy to test.
 */
final class StringUtils
{
    public const DEFAULT_TRUNCATE_SUFFIX = '...';

    private function __construct()
    {
    }

    public static function upper(string $value, string $encoding = 'UTF-8'): string
    {
        return mb_strtoupper($value, $encoding);
    }

    public static function lower(string $value, string $encoding = 'UTF-8'): string
    {
        return mb_strtolower($value, $encoding);
    }

    public static function ucfirst(string $value, string $encoding = 'UTF-8'): string
    {
        if ($value === '') {
            return '';
        }

        $first = mb_substr($value, 0, 1, $encoding);
        $rest  = mb_substr($value, 1, null, $encoding);

        return mb_strtoupper($first, $encoding) . $rest;
    }

    public static function trim(string $value, string $chars = " \t\n\r\0\x0B"): string
    {
        return trim($value, $chars);
    }

    public static function ltrim(string $value, string $chars = " \t\n\r\0\x0B"): string
    {
        return ltrim($value, $chars);
    }

    public static function rtrim(string $value, string $chars = " \t\n\r\0\x0B"): string
    {
        return rtrim($value, $chars);
    }

    /**
     * Returns the length for strings and Countable/arrays.
     */
    public static function length(mixed $value, string $encoding = 'UTF-8'): int
    {
        if (is_array($value) || $value instanceof Countable) {
            return count($value);
        }

        return mb_strlen((string) $value, $encoding);
    }

    /**
     * Truncates a string to $length characters and appends $suffix when needed.
     */
    public static function truncate(
        string $value,
        int $length = 12,
        string $suffix = self::DEFAULT_TRUNCATE_SUFFIX,
        string $encoding = 'UTF-8'
    ): string {
        if ($length <= 0) {
            return '';
        }

        if (mb_strlen($value, $encoding) <= $length) {
            return $value;
        }

        return mb_substr($value, 0, $length, $encoding) . $suffix;
    }

    public static function replace(string $value, string $search, string $replace): string
    {
        return str_replace($search, $replace, $value);
    }

    public static function regexReplace(string $value, string $pattern, string $replace): string
    {
        $result = preg_replace($pattern, $replace, $value);

        return $result ?? $value;
    }

    /**
     * Creates a URL-friendly slug while preserving Unicode letters and numbers.
     *
     * No ASCII transliteration is performed (ext-intl is not required).
     */
    public static function slug(string $value, string $separator = '-', string $encoding = 'UTF-8'): string
    {
        $value = self::lower($value, $encoding);

        // Trim punctuation before replacing internal groups, so custom separators
        // are not mistakenly stripped from legitimate text.
        $value = preg_replace('~^[^\p{L}\p{N}\p{M}]+|[^\p{L}\p{N}\p{M}]+$~u', '', $value) ?? '';

        return preg_replace_callback(
            '~[^\p{L}\p{N}\p{M}]+~u',
            static fn(): string => $separator,
            $value
        ) ?? '';
    }

    /**
     * Formats a date-like value.
     *
     * Supported inputs:
     * - DateTimeInterface
     * - numeric Unix timestamp
     * - string parsable by DateTimeImmutable
     */
    public static function formatDate(
        mixed $value,
        string $format = 'Y-m-d H:i',
        ?DateTimeZone $timezone = null
    ): string {
        if ($value instanceof DateTimeInterface) {
            if ($timezone === null) {
                return $value->format($format);
            }

            // Preserve fractional seconds and do not mutate mutable DateTime instances.
            return DateTimeImmutable::createFromInterface($value)
                ->setTimezone($timezone)
                ->format($format);
        }

        $timezone ??= new DateTimeZone(date_default_timezone_get());

        if (is_numeric($value)) {
            $dt = new DateTimeImmutable('@' . (int) $value)->setTimezone($timezone);
            return $dt->format($format);
        }

        if (is_string($value)) {
            try {
                $dt = new DateTimeImmutable($value, $timezone);
                return $dt->format($format);
            } catch (Exception) {
                return $value;
            }
        }

        return (string) $value;
    }

    public static function formatPrice(
        mixed $value,
        int $decimals = 2,
        string $decimalSeparator = ',',
        string $thousandsSeparator = ' '
    ): string {
        $float = is_numeric($value) ? (float) $value : 0.0;

        return number_format($float, $decimals, $decimalSeparator, $thousandsSeparator);
    }
}
