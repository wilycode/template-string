# Changelog

All notable changes to WilyCode Template String are documented here.

## [0.1.1] - 2026-11-14

### Improved

- Centralize the default `truncate` suffix (`...`) in `StringUtils::DEFAULT_TRUNCATE_SUFFIX` for both the utility and template filter.
- Clarify documentation and add UTF-8 regression coverage for the default and explicitly overridden suffixes.
- Preserve existing output: version 0.1.0 already used three ordinary dots by default.

## [0.1.0] - 2026-10-16

### Added

- Initial public Composer package `wilycode/template-string`.
- UTF-8 text filters, string replacements, date and price formatting.
- GitHub Actions test workflow and integration tests for registered filters.

### Fixed

- Preserve Unicode letters when generating slugs.
- Preserve microseconds when formatting a date in a different timezone.

### Compatibility

- Existing PHP namespaces and filter names are preserved.
