# Guisfus Meta Description

A lightweight WordPress plugin that outputs a `<meta name="description">` tag for singular content using a custom field, the post excerpt, or a trimmed version of the post content.

## Features

- Outputs a meta description on posts, pages, and custom post types.
- Uses a dedicated custom field: `guisfus_meta_description`.
- Keeps backward compatibility with the legacy field: `meta_description`.
- Falls back to the manual excerpt when no custom description is set.
- Falls back to trimmed post content when no custom field or excerpt exists.
- Registers the custom meta field for REST API compatibility.
- Skips output automatically when common SEO plugins are detected, helping avoid duplicate meta description tags.
- Limits the final description to 160 characters by default, without cutting words when possible.
- Provides filters for developers who want to customize or disable the output.

## Requirements

- WordPress 6.0 or higher.
- PHP 7.4 or higher.

## Installation

1. Download or clone this repository.
2. Copy the `guisfus-meta-description` folder into your WordPress `wp-content/plugins/` directory.
3. Activate **Guisfus Meta Description** from the WordPress admin plugins screen.

## Usage

Add a custom field to a post, page, or custom post type using this key:

```txt
guisfus_meta_description
```

The value of that field will be used as the page meta description.

For backward compatibility, the plugin also reads this legacy key:

```txt
meta_description
```

If neither field exists, the plugin will use the post excerpt. If there is no excerpt, it will generate a short description from the post content.

The final description is limited to 160 characters by default.

## SEO plugin compatibility

To avoid duplicate `<meta name="description">` tags, the plugin skips frontend output when it detects one of these common SEO plugins:

- Yoast SEO
- Rank Math
- SEOPress
- All in One SEO
- The SEO Framework

You can still customize this behavior with the developer filters below.

## Output priority

The plugin chooses the description in this order:

1. `guisfus_meta_description` custom field.
2. `meta_description` legacy custom field.
3. Manual post excerpt.
4. Trimmed post content.

## Developer filters

### Disable output

Use this filter to prevent the plugin from outputting the meta description, for example when another SEO plugin is active.

```php
add_filter( 'guisfus_meta_description_disable_output', '__return_true' );
```

### Modify the generated description

```php
add_filter(
	'guisfus_meta_description_value',
	function ( $description, $post ) {
		return $description;
	},
	10,
	2
);
```

### Change the maximum description length

```php
add_filter(
	'guisfus_meta_description_max_length',
	function () {
		return 155;
	}
);
```

Return `0` to disable the character limit.

### Override SEO plugin detection

```php
add_filter(
	'guisfus_meta_description_has_seo_plugin',
	function ( $has_seo_plugin ) {
		return $has_seo_plugin;
	}
);
```

Return `false` from this filter if you want this plugin to output the meta description even when a known SEO plugin is active.

## Repository structure

```txt
guisfus-meta-description/
├── guisfus-meta-description.php
├── README.md
├── readme.txt
├── LICENSE
└── .gitignore
```

## Notes

This plugin is intentionally small. It does not add an editor UI, SEO analysis, sitemap generation, schema markup, Open Graph tags, or social preview fields.

If you already use a full SEO plugin such as Yoast SEO, Rank Math, SEOPress, or The SEO Framework, you probably do not need this plugin unless you specifically want a lightweight custom meta description fallback.

## License

GPL-2.0-or-later. See the `LICENSE` file for details.
