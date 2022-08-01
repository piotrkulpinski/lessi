<?php

namespace Piotrkulpinski\Framework\Helper;

/**
 * Provides aliases for main core WordPress features
 */
trait CoreTrait {

	/**
	 * Registers theme support for a given feature.
	 *
	 * @param mixed[] $args,...
	 */
	protected function addSupport( ...$args ) {
		return add_theme_support( ...$args );
	}

	/**
	 * Registers custom image size
	 *
	 * @param mixed[] $args,...
	 */
	protected function addImageSize( ...$args ) {
		return add_image_size( ...$args );
	}

	/**
	 * Registers custom navigation menu
	 *
	 * @param mixed $args,...
	 */
	protected function addNavMenu( ...$args ) {
		return register_nav_menu( ...$args );
	}

	/**
	 * Loads the theme’s translated strings.
	 *
	 * @param mixed[] $args,...
	 */
	protected function addTextDomain( ...$args ) {
		return load_theme_textdomain( ...$args );
	}
}
