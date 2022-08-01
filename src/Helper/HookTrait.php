<?php

namespace Piotrkulpinski\Framework\Helper;

use Piotrkulpinski\Framework\Helper\HelperTrait;

/**
 * Provides methods to register new hooks in the theme
 */
trait HookTrait {

	use HelperTrait;

	/**
	 * Passes its arguments to add_action().
	 *
	 * @param string $hook
	 * @param mixed  $callback
	 * @param int    $priority
	 * @param int    $args
	 *
	 * @return bool|null
	 */
	protected function addAction( string $hook, $callback, int $priority = 10, int $args = 1 ): ?bool {
		if ( ! is_callable( $callback ) ) {
			// TODO: Implement a proper error logging here
			return null;
		}

		return is_string( $callback )
			? add_action( $hook, [ $this, $callback ], $priority, $args )
			: add_action( $hook, $callback, $priority, $args );
	}

	/**
	 * Passes its arguments to add_filter.
	 *
	 * @param string $hook
	 * @param mixed  $callback
	 * @param int    $priority
	 * @param int    $args
	 *
	 * @return bool|null
	 */
	protected function addFilter( string $hook, $callback, int $priority = 10, int $args = 1 ): ?bool {
		if ( ! is_callable( $callback ) ) {
			// TODO: Implement a proper error logging here
			return null;
		}

		return is_string( $callback )
			? add_filter( $hook, [ $this, $callback ], $priority, $args )
			: add_filter( $hook, $callback, $priority, $args );
	}

	/**
	 * Generate proper AJAX hook names and asses its arguments to add_action().
	 *
	 * @param string $hook
	 * @param mixed  $callback
	 * @param int    $priority
	 * @param int    $args
	 *
	 * @return bool|null
	 */
	protected function addAjaxAction( string $hook, $callback, int $priority = 10, int $args = 1 ): ?bool {
		if ( ! is_callable( $callback ) ) {
			// TODO: Implement a proper error logging here
			return null;
		}

		$this->addAction( 'wp_ajax_' . $this->getThemeSlug( $hook ), $callback, $priority, $args );
		$this->addAction( 'wp_ajax_nopriv_' . $this->getThemeSlug( $hook ), $callback, $priority, $args );

		return true;
	}

	/**
	 * Passes its arguments to add_filter and adds a Hook to our collection.
	 *
	 * @param string $hook
	 * @param mixed  $value
	 * @param mixed  $args
	 *
	 * @return mixed
	 */
	protected function applyFilter( string $hook, $value, bool $prefix = true, ...$args ) {
		$hookName = $prefix ? $this->getThemeSlug( $hook ) : $hook;

		return apply_filters( $hookName, $value, ...$args );
	}
}
