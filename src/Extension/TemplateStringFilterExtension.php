<?php

declare(strict_types=1);

namespace WilyCode\Template\Extension;

use WilyCode\Template\TemplateEngine;
use WilyCode\Template\TemplateExtensionInterface;
use WilyCode\Template\Util\StringUtils;

/**
 * Registers common string / formatting filters:
 *
 * - upper, lower, ucfirst
 * - trim, ltrim, rtrim
 * - length
 * - truncate
 * - replace, regex_replace
 * - slug
 * - date
 * - price
 */
final class TemplateStringFilterExtension implements TemplateExtensionInterface
{
    public function __construct(
        private readonly string $encoding = 'UTF-8',
        private readonly int $defaultTruncateLength = 12,
        private readonly string $defaultTruncateSuffix = '...',
        private readonly int $defaultPriceDecimals = 2,
        private readonly string $defaultPriceDecimalSeparator = ',',
        private readonly string $defaultPriceThousandsSeparator = ' '
    ) {
    }

    public function register(TemplateEngine $engine): void
    {
        $enc = $this->encoding;

        $engine->addFilter('upper', function (mixed $value) use ($enc): string {
            return StringUtils::upper((string) $value, $enc);
        });

        $engine->addFilter('lower', function (mixed $value) use ($enc): string {
            return StringUtils::lower((string) $value, $enc);
        });

        $engine->addFilter('ucfirst', function (mixed $value) use ($enc): string {
            return StringUtils::ucfirst((string) $value, $enc);
        });

        $engine->addFilter('trim', function (mixed $value, string $chars = " \t\n\r\0\x0B"): string {
            return StringUtils::trim((string) $value, $chars);
        });

        $engine->addFilter('ltrim', function (mixed $value, string $chars = " \t\n\r\0\x0B"): string {
            return StringUtils::ltrim((string) $value, $chars);
        });

        $engine->addFilter('rtrim', function (mixed $value, string $chars = " \t\n\r\0\x0B"): string {
            return StringUtils::rtrim((string) $value, $chars);
        });

        $engine->addFilter('length', function (mixed $value) use ($enc): int {
            return StringUtils::length($value, $enc);
        });

        $engine->addFilter(
            'truncate',
            function (mixed $value, ?int $length = null, ?string $suffix = null) use ($enc): string {
                $length ??= $this->defaultTruncateLength;
                $suffix ??= $this->defaultTruncateSuffix;

                return StringUtils::truncate((string) $value, $length, $suffix, $enc);
            }
        );

        $engine->addFilter('replace', function (mixed $value, string $search, string $replace): string {
            return StringUtils::replace((string) $value, $search, $replace);
        });

        $engine->addFilter(
            'regex_replace',
            function (mixed $value, string $pattern, string $replace): string {
                return StringUtils::regexReplace((string) $value, $pattern, $replace);
            }
        );

        $engine->addFilter(
            'slug',
            function (mixed $value, string $separator = '-') use ($enc): string {
                return StringUtils::slug((string) $value, $separator, $enc);
            }
        );

        $engine->addFilter(
            'date',
            function (mixed $value, string $format = 'Y-m-d H:i'): string {
                return StringUtils::formatDate($value, $format);
            }
        );

        $engine->addFilter(
            'price',
            function (
                mixed $value,
                ?int $decimals = null,
                ?string $decimalSeparator = null,
                ?string $thousandsSeparator = null
            ): string {
                $decimals           ??= $this->defaultPriceDecimals;
                $decimalSeparator   ??= $this->defaultPriceDecimalSeparator;
                $thousandsSeparator ??= $this->defaultPriceThousandsSeparator;

                return StringUtils::formatPrice(
                    $value,
                    $decimals,
                    $decimalSeparator,
                    $thousandsSeparator
                );
            }
        );
    }
}
