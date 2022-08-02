<?php

namespace MadeByLess\Lessi\Helper;

use function wp_get_theme;

/**
 * Provides methods to retrieve theme settings
 */
trait ThemeTrait {

	/**
	 * Retrieves theme name. Used for naming assets handlers, languages, etc.
	 *
	 * @return string
	 */
	protected function getName(): string {
		return ( wp_get_theme() )->Name;
	}

	/**
	 * Retrieves theme slug. Used for naming settings, customizer options etc.
	 *
	 * @return string
	 */
	protected function getSlug(): string {
		return get_stylesheet();
	}

	/**
	 * Retrieves theme version. Used for versioning asset handlers while enqueueing them.
	 *
	 * @return string
	 */
	protected function getVersion(): string {
		return ( wp_get_theme() )->Version;
	}

	/**
	 * Retrieves theme author. Used for displaying author on theme settings.
	 *
	 * @return string
	 */
	protected function getAuthor(): string {
		return ( wp_get_theme() )->Author;
	}
}
