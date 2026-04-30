=== Meta Description ===
Contributors: guisfus
Tags: meta description, seo, metadata, custom fields, gutenberg
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Outputs a lightweight meta description tag for singular WordPress content using a custom field, excerpt, or trimmed content.

== Description ==

Meta Description is a small WordPress plugin that outputs a `<meta name="description">` tag for posts, pages, and custom post types.

The plugin is intentionally lightweight. It does not provide SEO analysis, schema markup, Open Graph tags, sitemaps, or a custom editor interface. It simply provides a clean fallback system for meta descriptions.

To avoid duplicate meta description tags, the plugin automatically skips frontend output when it detects common SEO plugins such as Yoast SEO, Rank Math, SEOPress, All in One SEO, or The SEO Framework.

The final description is not limited by default. Developers can enable a maximum length with a filter.

The plugin chooses the description in this order:

1. `meta_description` custom field.
2. Manual post excerpt.
3. Trimmed post content.

== Installation ==

The GitHub repository uses the `wp-` prefix only to identify it as a WordPress plugin repository. When installing the plugin in WordPress, use the plugin folder name without the `wp-` prefix.

Correct plugin folder: `/wp-content/plugins/meta-description/`

Correct ZIP structure: `meta-description.zip` containing a root `meta-description/` folder with `meta-description.php` inside it.

Do not install it as `/wp-content/plugins/wp-meta-description/`.

Backend installation:

1. Create a ZIP with `meta-description/` as the root folder.
2. Go to Plugins > Add New > Upload Plugin.
3. Upload `meta-description.zip`.
4. Activate Meta Description.

Manual installation:

1. Upload the `meta-description` folder to `/wp-content/plugins/`.
2. Activate Meta Description from the WordPress plugins screen.
3. Add a custom field named `meta_description` to any post, page, or custom post type.

== Frequently Asked Questions ==

= Which custom field should I use? =

Use `meta_description`.

The key is intentionally simple for custom sites. If a project already uses that field name for another purpose, rename the field in the plugin before deploying it.

= Does this plugin add a Gutenberg sidebar or editor field? =

No. This first version only registers and reads the custom meta field. It does not add a custom editor UI.

= What happens when the custom field is empty? =

The plugin falls back to the manual post excerpt. If the excerpt is empty, it generates a short description from the post content.

= Can I use this with another SEO plugin? =

You should avoid outputting duplicate meta description tags. If another supported SEO plugin already handles meta descriptions, this plugin will skip its own frontend output by default.

Developers can disable the output with this filter:

`add_filter( 'meta_description_disable_output', '__return_true' );`

Developers can override SEO plugin detection with this filter:

`add_filter( 'meta_description_has_seo_plugin', '__return_false' );`

= Can developers modify the final description? =

Yes. Use the `meta_description_value` filter.

= Can developers set a maximum description length? =

Yes. Use the `meta_description_max_length` filter. The default value is `0`, which disables the limit.

= Does this plugin enqueue frontend assets? =

No. It only outputs one escaped `<meta name="description">` tag when a description is available and no supported SEO plugin is detected.

== Changelog ==

= 1.0.1 =
* Disable the maximum description length by default so manually written descriptions are not truncated.

= 1.0.0 =
* Initial release.
