<?php

namespace MadeByLess\Lessi\Helper;

use Dashifen\CaseChangingTrait\CaseChangingTrait;

/**
 * Provides methods to retrieve theme properties
 */
trait ThemeTrait {
	use CaseChangingTrait;

	/**
	 * Retrieves theme object or property if passed.
	 * Used for naming assets handlers, languages, etc.
	 *
	 * @param ?string $property
	 *
	 * @return object|string|null
	 */
	public function getThemeProperty( ?string $property = null ) {
		$theme = wp_get_theme();

		if ( $theme->exists() ) {
			if ( ! empty( $property ) ) {
				return $theme->get( $this->kebabToPascalCase( $property ) );
			} else {
				return $theme;
			}
		}

		return null;
	}
}
