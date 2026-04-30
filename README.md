# Meta Description

A lightweight WordPress plugin that outputs a `<meta name="description">` tag for singular content using a custom field, the post excerpt, or a trimmed version of the post content.

## Features

- Outputs a meta description on posts, pages, and custom post types.
- Uses a dedicated custom field: `meta_description`.
- Falls back to the manual excerpt when no custom description is set.
- Falls back to trimmed post content when no custom field or excerpt exists.
- Registers the custom meta field for REST API compatibility.
- Skips output automatically when common SEO plugins are detected, helping avoid duplicate meta description tags.
- Provides an optional filter to limit the final description length, without cutting words when possible.
- Provides filters for developers who want to customize or disable the output.

## Requirements

- WordPress 6.0 or higher.
- PHP 7.4 or higher.

## Installation

The GitHub repository uses the `wp-` prefix only to identify it as a WordPress plugin repository. When installing the plugin in WordPress, use the plugin folder name without the `wp-` prefix.

Correct plugin folder:

```txt
wp-content/plugins/meta-description/
```

Correct ZIP structure:

```txt
meta-description.zip
`-- meta-description/
    |-- meta-description.php
    |-- README.md
    |-- readme.txt
    `-- LICENSE
```

Do not install it as:

```txt
wp-content/plugins/wp-meta-description/
```

Backend installation:

1. Create a ZIP with `meta-description/` as the root folder.
2. In WordPress, go to **Plugins > Add New > Upload Plugin**.
3. Upload `meta-description.zip`.
4. Activate **Meta Description**.

Manual installation:

1. Upload the `meta-description` folder to `wp-content/plugins/`.
2. Activate **Meta Description** from the WordPress plugins screen.

## Usage

Add a custom field to a post, page, or custom post type using this key:

```txt
meta_description
```

The value of that field will be used as the page meta description.

The `meta_description` key is intentionally simple for custom sites. If a project already uses that field name for another purpose, rename the field in the plugin before deploying it.

If the field does not exist, the plugin will use the post excerpt. If there is no excerpt, it will generate a short description from the post content.

The final description is not limited by default, so custom descriptions are output as entered after sanitization and escaping.

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

1. `meta_description` custom field.
2. Manual post excerpt.
3. Trimmed post content.

## Developer filters

### Disable output

Use this filter to prevent the plugin from outputting the meta description, for example when another SEO plugin is active.

```php
add_filter( 'meta_description_disable_output', '__return_true' );
```

### Modify the generated description

```php
add_filter(
	'meta_description_value',
	function ( $description, $post ) {
		return $description;
	},
	10,
	2
);
```

### Set a maximum description length

```php
add_filter(
	'meta_description_max_length',
	function () {
		return 155;
	}
);
```

Return `0` to disable the character limit.

### Override SEO plugin detection

```php
add_filter(
	'meta_description_has_seo_plugin',
	function ( $has_seo_plugin ) {
		return $has_seo_plugin;
	}
);
```

Return `false` from this filter if you want this plugin to output the meta description even when a known SEO plugin is active.

## Security

- Outputs only on singular content.
- Escapes the final tag value with `esc_attr()`.
- Sanitizes stored and generated descriptions with WordPress text sanitization helpers.
- Registers the post meta field with an edit capability check.
- Does not add admin forms, AJAX actions, REST routes, external requests, cookies, or tracking.

## Frontend Footprint

- Outputs one `<meta name="description">` tag when no supported SEO plugin is detected.
- Does not enqueue frontend CSS or JavaScript.
- Does not write visible HTML comments or branded markup.

## Repository structure

```txt
meta-description/
|-- meta-description.php
|-- README.md
|-- readme.txt
|-- LICENSE
`-- .gitignore
```

## Notes

This plugin is intentionally small. It does not add an editor UI, SEO analysis, sitemap generation, schema markup, Open Graph tags, or social preview fields.

If you already use a full SEO plugin such as Yoast SEO, Rank Math, SEOPress, or The SEO Framework, you probably do not need this plugin unless you specifically want a lightweight custom meta description fallback.

## License

GPL-2.0-or-later. See the `LICENSE` file for details.
