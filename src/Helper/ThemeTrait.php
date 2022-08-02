<?php

namespace MadeByLess\Lessi\Helper;

/**
 * Provides methods to retrieve theme settings
 */
trait ThemeTrait {

	/**
	 * Retrieves theme slug. Used for naming settings, customizer options etc.
	 *
	 * @return string
	 */
	protected function getThemeSlug(): string {
		return get_stylesheet();
	}

	/**
	 * Retrieves theme name. Used for naming assets handlers, languages, etc.
	 *
	 * @return string
	 */
	protected function getThemeName(): string {
		return ( wp_get_theme() )->Name;
	}

	/**
	 * Retrieves theme version. Used for versioning asset handlers while enqueueing them.
	 *
	 * @return string
	 */
	protected function getThemeVersion(): string {
		return ( wp_get_theme() )->Version;
	}

	/**
	 * Retrieves theme author. Used for displaying author on theme settings.
	 *
	 * @return string
	 */
	protected function getThemeAuthor(): string {
		return ( wp_get_theme() )->Author;
	}
}
