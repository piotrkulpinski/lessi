<?php

namespace MadeByLess\Lessi\Helper;

/**
 * Provides methods for selectors and HTML elements
 */
trait SelectorTrait {

	/**
	 * Builds class string based on name and modifiers
	 *
	 * @param string               $name          Base class name
	 * @param string[]|string|null $modifiers,... Class name modifiers
	 *
	 * @return string
	 */
	public function className( string $name, $modifiers = null ): string {
		if ( ! is_string( $name ) ) {
			return '';
		}

		$modifiers = array_slice( func_get_args(), 1 );
		$classes   = [ $name ];

		foreach ( $modifiers as $modifier ) {
			if ( ! empty( $modifier ) ) {
				if ( is_array( $modifier ) ) {
					foreach ( $modifier as $modifier ) {
						if ( ! empty( $modifier ) ) {
							$classes[] = $name . '--' . $modifier;
						}
					}
				} elseif ( is_string( $modifier ) ) {
					$classes[] = $name . '--' . $modifier;
				}
			}
		}

		return implode( ' ', $classes );
	}

	/**
	 * Makes sure the output is string. Useful for converting an array of components into a string.
	 * If you pass an associative array it will output strings with keys, used for generating data-attributes from array.
	 *
	 * @param array<string, mixed>|string[]|string $variable Variable we need to convert into a string.
	 *
	 * @return ?string
	 */
	public function ensureString( $variable ): string {
		$output = '';

		if ( is_array( $variable ) ) {
			$isAssociative = array_values( $variable ) === $variable;

			if ( $isAssociative ) {
				$output = implode( '', $variable );
			} else {
				foreach ( $variable as $key => $value ) {
					$output .= $key . '="' . htmlspecialchars( $value ) . '" ';
				}
			}
		} elseif ( is_string( $variable ) ) {
			$output = $variable;
		} else {
			// TODO: Implement a proper error logging here
			return null;
		}

		return $output;
	}

	/**
	 * Highlight part of the input with chosen tag
	 *
	 * @param string $value   Value to highlight from
	 * @param string $pattern RegEx pattern to search from
	 * @param string $tag     HTML tag to replace the pattern with
	 *
	 * @return string
	 */
	public static function highlight( string $value, string $pattern = '**', string $tag = 'strong' ): string {
		if ( empty( $value ) ) {
			return null;
		}

		$pattern     = preg_quote( $pattern, '/' );
		$pattern     = "/{$pattern}([^*]*){$pattern}/i";
		$replacement = "<{$tag}>$1</{$tag}>";

		return preg_replace( $pattern, $replacement, $value );
	}
}
