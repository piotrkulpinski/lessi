<?php

namespace Piotrkulpinski\Framework\Helper;

use Timber\Timber;

/**
 * Provides methods to register new shortcodes in the theme
 */
trait ShortcodeTrait {

	/**
	 * Passes its arguments to add_shortcode().
	 *
	 * @param string   $tag
	 * @param callable $callback
	 */
	protected function addShortcode( string $tag, callable $callback ) {
		return is_string( $callback )
			? add_shortcode( $tag, [ $this, $callback ] )
			: add_shortcode( $tag, $callback );
	}

	/**
	 * Calls a shortcode function by tag name.
	 *
	 * @param string       $tag The shortcode whose function to call.
	 * @param array<mixed> $atts The attributes to pass to the shortcode function. Optional.
	 * @param string|null  $content The shortcode's content. Default is null (none).
	 *
	 * @return string|bool False on failure, the result of the shortcode on success.
	 */
	protected function getShortcode( string $tag, array $attr = [], ?string $content = null ) {
		global $shortcode_tags;

		if ( ! isset( $shortcode_tags[ $tag ] ) ) {
			return false;
		}

		return call_user_func( $shortcode_tags[ $tag ], $attr, $content, $tag );
	}

	/**
	 * Retrieves shortcode template and passes attributes to it.
	 *
	 * @param string $template
	 * @param array  $atts
	 *
	 * @return string
	 */
	protected function getShortcodeTemplate( string $template, array $atts = [] ): string {
		return Timber::compile( $template, array_merge( Timber::context(), $atts ) );
	}
}
