# Changelog

## 1.0.1

- Disables the maximum description length by default so manually written descriptions are not truncated.
- Keeps the `meta_description_max_length` filter available for sites that want to enforce a custom limit.

## 1.0.0

- Initial release.
- Adds a frontend meta description tag for singular content.
- Supports `meta_description` as the primary custom field.
- Falls back to post excerpt or trimmed content when no custom field is set.
- Adds SEO plugin detection to avoid duplicate meta description output.
- Adds an optional configurable maximum description length, disabled by default.
- Adds explicit REST schema registration for the custom meta field.
- Adds plugin translation loading.
