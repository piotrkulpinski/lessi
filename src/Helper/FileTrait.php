<?php

namespace MadeByLess\Lessi\Helper;

/**
 * Provides methods to run file-related tasks
 */
trait FileTrait {

	/**
	 * Builds a file path with the appropriate directory separator.
	 *
	 * @param string[] $segments,... unlimited number of path segments
	 *
	 * @return string Path
	 */
	protected function getTemplatePath( ...$segments ): string {
		array_unshift( $segments, get_template_directory() );

		return $this->getPath( ...$segments );
	}

	/**
	 * Builds a file path with the appropriate directory separator.
	 *
	 * @param string[] $segments,... unlimited number of path segments
	 *
	 * @return string Path
	 */
	protected function getTemplateUrl( ...$segments ): string {
		array_unshift( $segments, get_template_directory_uri() );

		return $this->getPath( ...$segments );
	}

	/**
	 * Builds a file path with the appropriate directory separator.
	 *
	 * @param string[] $segments,... unlimited number of path segments
	 *
	 * @return string Path
	 */
	protected function getPath( ...$segments ): string {
		return join( DIRECTORY_SEPARATOR, $segments );
	}
}
