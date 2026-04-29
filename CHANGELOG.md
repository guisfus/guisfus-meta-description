# Changelog

## 1.0.0

- Initial release.
- Adds a frontend meta description tag for singular content.
- Supports `guisfus_meta_description` as the primary custom field.
- Supports `meta_description` as a legacy fallback field.
- Falls back to post excerpt or trimmed content when no custom field is set.
- Adds SEO plugin detection to avoid duplicate meta description output.
- Adds a configurable 160-character maximum description length.
- Adds explicit REST schema registration for the custom meta field.
- Adds plugin translation loading.
