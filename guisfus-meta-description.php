<?php
/**
 * Plugin Name: Guisfus Meta Description
 * Plugin URI: https://github.com/guisfus/guisfus-meta-description
 * Description: Outputs a meta description tag for singular content using a custom field, excerpt, or trimmed content.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: guisfus
 * Author URI: https://github.com/guisfus
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: guisfus-meta-description
 *
 * @package GuisfusMetaDescription
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Guisfus_Meta_Description' ) ) {
	/**
	 * Main plugin class.
	 */
	final class Guisfus_Meta_Description {

		/**
		 * Plugin version.
		 */
		private const VERSION = '1.0.0';

		/**
		 * Primary custom field key.
		 */
		private const META_KEY = 'guisfus_meta_description';

		/**
		 * Backward-compatible custom field key used by earlier versions/snippets.
		 */
		private const LEGACY_META_KEY = 'meta_description';

		/**
		 * Fallback description length in words.
		 */
		private const FALLBACK_WORD_LIMIT = 25;

		/**
		 * Maximum generated description length in characters.
		 */
		private const DESCRIPTION_MAX_LENGTH = 160;

		/**
		 * Boot the plugin.
		 */
		public static function init(): void {
			add_action( 'plugins_loaded', array( __CLASS__, 'load_textdomain' ) );
			add_action( 'init', array( __CLASS__, 'register_meta' ) );
			add_action( 'wp_head', array( __CLASS__, 'render_meta_description' ), 1 );
		}

		/**
		 * Load plugin translations.
		 */
		public static function load_textdomain(): void {
			load_plugin_textdomain(
				'guisfus-meta-description',
				false,
				dirname( plugin_basename( __FILE__ ) ) . '/languages'
			);
		}

		/**
		 * Register the custom meta field for posts, pages, and custom post types.
		 */
		public static function register_meta(): void {
			register_meta(
				'post',
				self::META_KEY,
				array(
					'type'              => 'string',
					'description'       => __( 'Custom meta description for search engines and social previews.', 'guisfus-meta-description' ),
					'single'            => true,
					'sanitize_callback' => array( __CLASS__, 'sanitize_description' ),
					'auth_callback'     => array( __CLASS__, 'can_edit_meta' ),
					'show_in_rest'      => array(
						'schema' => array(
							'type' => 'string',
						),
					),
				)
			);
		}

		/**
		 * Check whether the current user can edit the meta value.
		 *
		 * @param bool   $allowed   Whether the user is allowed by default.
		 * @param string $meta_key  Meta key name.
		 * @param int    $object_id Post ID.
		 * @return bool
		 */
		public static function can_edit_meta( $allowed, $meta_key, $object_id ): bool {
			unset( $allowed, $meta_key );

			return current_user_can( 'edit_post', (int) $object_id );
		}

		/**
		 * Sanitize the meta description value.
		 *
		 * @param mixed $value Raw value.
		 * @return string
		 */
		public static function sanitize_description( $value ): string {
			if ( ! is_string( $value ) ) {
				return '';
			}

			$value = wp_strip_all_tags( $value );
			$value = sanitize_text_field( $value );

			return trim( $value );
		}

		/**
		 * Output the meta description tag in the document head.
		 */
		public static function render_meta_description(): void {
			if ( ! is_singular() ) {
				return;
			}

			if ( self::has_known_seo_plugin() ) {
				return;
			}

			$post_id = get_queried_object_id();

			if ( ! $post_id ) {
				return;
			}

			$post = get_post( $post_id );

			if ( ! $post instanceof WP_Post ) {
				return;
			}

			/**
			 * Filter whether the plugin should skip outputting the meta description.
			 *
			 * Useful when another SEO plugin is already outputting a meta description.
			 *
			 * @param bool    $disable Whether to disable output. Default false.
			 * @param WP_Post $post    Current post object.
			 */
			if ( (bool) apply_filters( 'guisfus_meta_description_disable_output', false, $post ) ) {
				return;
			}

			$description = self::get_description( $post );

			if ( '' === $description ) {
				return;
			}

			printf(
				'<meta name="description" content="%s">%s',
				esc_attr( $description ),
				"\n"
			);
		}

		/**
		 * Get the best available meta description for a post.
		 *
		 * @param WP_Post $post Current post object.
		 * @return string
		 */
		private static function get_description( WP_Post $post ): string {
			$description = get_post_meta( $post->ID, self::META_KEY, true );

			if ( '' === $description ) {
				$description = get_post_meta( $post->ID, self::LEGACY_META_KEY, true );
			}

			if ( '' === $description && has_excerpt( $post ) ) {
				$description = get_the_excerpt( $post );
			}

			if ( '' === $description ) {
				$description = wp_trim_words(
					wp_strip_all_tags( $post->post_content ),
					self::FALLBACK_WORD_LIMIT,
					''
				);
			}

			/**
			 * Filter the generated meta description before output.
			 *
			 * @param string  $description Meta description text.
			 * @param WP_Post $post        Current post object.
			 */
			$description = (string) apply_filters( 'guisfus_meta_description_value', $description, $post );

			return self::limit_description( self::sanitize_description( $description ) );
		}

		/**
		 * Check whether a known SEO plugin is active and likely already outputs a meta description.
		 *
		 * @return bool
		 */
		private static function has_known_seo_plugin(): bool {
			$has_seo_plugin = defined( 'WPSEO_VERSION' )
				|| defined( 'RANK_MATH_VERSION' )
				|| defined( 'SEOPRESS_VERSION' )
				|| defined( 'AIOSEO_VERSION' )
				|| defined( 'THE_SEO_FRAMEWORK_VERSION' )
				|| class_exists( 'WPSEO_Frontend' )
				|| class_exists( 'RankMath' )
				|| class_exists( 'SEOPress' )
				|| class_exists( 'AIOSEO\Plugin\AIOSEO' )
				|| function_exists( 'the_seo_framework' );

			/**
			 * Filter whether the plugin should skip output because a known SEO plugin is active.
			 *
			 * @param bool $has_seo_plugin Whether a known SEO plugin was detected.
			 */
			return (bool) apply_filters( 'guisfus_meta_description_has_seo_plugin', $has_seo_plugin );
		}

		/**
		 * Limit the description length without cutting words when possible.
		 *
		 * @param string $description Description text.
		 * @return string
		 */
		private static function limit_description( string $description ): string {
			/**
			 * Filter the maximum meta description length in characters.
			 *
			 * @param int $max_length Maximum character length. Default 160.
			 */
			$max_length = (int) apply_filters( 'guisfus_meta_description_max_length', self::DESCRIPTION_MAX_LENGTH );

			if ( $max_length < 1 || self::string_length( $description ) <= $max_length ) {
				return $description;
			}

			$description = self::string_substr( $description, 0, $max_length + 1 );
			$last_space  = self::string_strrpos( $description, ' ' );

			if ( false !== $last_space ) {
				$description = self::string_substr( $description, 0, $last_space );
			} else {
				$description = self::string_substr( $description, 0, $max_length );
			}

			return rtrim( $description, " \t\n\r\0\x0B.,;:-" );
		}

		/**
		 * Get string length with multibyte support when available.
		 *
		 * @param string $value String value.
		 * @return int
		 */
		private static function string_length( string $value ): int {
			if ( function_exists( 'mb_strlen' ) ) {
				return mb_strlen( $value );
			}

			return strlen( $value );
		}

		/**
		 * Get a substring with multibyte support when available.
		 *
		 * @param string   $value  String value.
		 * @param int      $start  Start offset.
		 * @param int|null $length Optional length.
		 * @return string
		 */
		private static function string_substr( string $value, int $start, ?int $length = null ): string {
			if ( function_exists( 'mb_substr' ) ) {
				return mb_substr( $value, $start, $length );
			}

			if ( null === $length ) {
				return substr( $value, $start );
			}

			return substr( $value, $start, $length );
		}

		/**
		 * Find the last position of a substring with multibyte support when available.
		 *
		 * @param string $haystack String to search in.
		 * @param string $needle   String to search for.
		 * @return int|false
		 */
		private static function string_strrpos( string $haystack, string $needle ) {
			if ( function_exists( 'mb_strrpos' ) ) {
				return mb_strrpos( $haystack, $needle );
			}

			return strrpos( $haystack, $needle );
		}
	}

	Guisfus_Meta_Description::init();
}
