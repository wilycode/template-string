# WilyCode Template String

[![PHP 8.4+](https://img.shields.io/badge/PHP-8.4%2B-777BB4)](https://www.php.net/)
[![MIT License](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![Tests](https://github.com/wilycode/template-string/actions/workflows/tests.yml/badge.svg)](https://github.com/wilycode/template-string/actions/workflows/tests.yml)

**UTF-8 string and formatting filters for the [WilyCode Template](https://github.com/wilycode/template) engine, including text transformations, truncation, replacements, slugs, dates and prices.**

> ### WilyCode PHP Ecosystem
>
> This library is part of **[WilyCode](https://github.com/wilycode/wilycode)**, a collection of independent PHP components. Each package has its own repository, Composer installation and releases.
>
> **[Explore the WilyCode ecosystem →](https://github.com/wilycode/wilycode)**

The package adds filters without changing the `.tpl` syntax or the template compiler.

## Installation

```bash
composer require wilycode/template-string
```

Requirements: PHP 8.4+, `ext-mbstring`, and `wilycode/template:^0.1`.

## Register the extension

```php
use WilyCode\Template\Extension\TemplateStringFilterExtension;
use WilyCode\Template\FileLocator;
use WilyCode\Template\TemplateEngine;
use WilyCode\Template\TemplateLoader;

$loader = new TemplateLoader(new FileLocator(['' => __DIR__ . '/templates']));
$engine = new TemplateEngine($loader, __DIR__ . '/cache');

$engine->addExtension(new TemplateStringFilterExtension());

echo $engine->render('home', ['name' => 'WilyCode']);
```

The existing PHP namespaces remain `WilyCode\Template\Extension` and `WilyCode\Template\Util`.

## Available filters

| Filter | Description | Example |
| --- | --- | --- |
| `upper` | Uppercase (UTF-8) | `{{ $name \| upper }}` |
| `lower` | Lowercase (UTF-8) | `{{ $name \| lower }}` |
| `ucfirst` | Uppercase first character (UTF-8) | `{{ $name \| ucfirst }}` |
| `trim` | Trim both ends | `{{ $name \| trim }}` |
| `ltrim` | Trim left | `{{ $name \| ltrim }}` |
| `rtrim` | Trim right | `{{ $name \| rtrim }}` |
| `length` | UTF-8 string length or count of an array/Countable | `{{ $tags \| length }}` |
| `truncate` | Shorten text; default length 12 and suffix `...` | `{{ $text \| truncate(20) }}` |
| `replace` | Literal replacement | `{{ $name \| replace(' ', '-') }}` |
| `regex_replace` | PCRE replacement | `{{ $name \| regex_replace('/\\s+/', '-') }}` |
| `slug` | Unicode-aware URL segment; no ASCII transliteration | `{{ $title \| slug }}` |
| `date` | Format DateTime objects, timestamps or date strings | `{{ $createdAt \| date('Y-m-d') }}` |
| `price` | Format a numeric amount; default `1 234,56` | `{{ $price \| price(2, '.', ',') }}` |

For example, in `home.tpl`:

```html
<h1>{{ $name | upper }}</h1>
<p>{{ 'Zażółć gęślą jaźń' | slug }}</p>
<p>{{ $price | price }}</p>
```

The `slug` filter preserves Unicode letters, including Polish diacritics. It does not transliterate to ASCII. `regex_replace` accepts regular expressions from trusted template authors; do not construct regex patterns from untrusted input.

By default, `truncate` appends **three ordinary dots** (`...`) when shortening text, not the single Unicode ellipsis character (`…`). To override the suffix, use `{{ $text | truncate(20, '…') }}`.

### Custom defaults

All options are optional:

```php
$engine->addExtension(new TemplateStringFilterExtension(
    encoding: 'UTF-8',
    defaultTruncateLength: 40,
    defaultTruncateSuffix: ' (more)',
    defaultPriceDecimals: 2,
    defaultPriceDecimalSeparator: ',',
    defaultPriceThousandsSeparator: ' ',
));
```

## Development

```bash
composer install
composer test
```

Tests and GitHub Actions workflows are stored in the source repository. Development-only files are excluded from release archives.

## Related libraries

- [WilyCode Template](https://github.com/wilycode/template) — template engine.
- [WilyCode Template Assets](https://github.com/wilycode/template-assets) — asset URL helpers.
- [WilyCode Template Translation](https://github.com/wilycode/template-translation) — translation integration.

## License

MIT. See [LICENSE](LICENSE).
