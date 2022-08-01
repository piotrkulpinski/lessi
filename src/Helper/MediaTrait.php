<?php

namespace Piotrkulpinski\Framework\Helper;

/**
 * Provides methods related to media files
 */
trait MediaTrait {

	/**
	 * Pulls the image content into the svg markup
	 *
	 * @param string $path File path
	 *
	 * @return string
	 */
	public function getSvgContent( string $path ): string {
		if ( ! empty( $path ) && $svgFile = @ file_get_contents( $path ) ) {
			$position = strpos( $svgFile, '<svg' );
			return substr( $svgFile, $position );
		}

		return "<img src='$path' alt='' />";
	}

	/**
	 * Converts svg content to base64 encoded
	 *
	 * @param string $path File path
	 *
	 * @return ?string
	 */
	public function svgToBase64( string $path ): ?string {
		if ( ! empty( $path ) && $svgFile = @ file_get_contents( $path ) ) {
			return 'data:image/svg+xml;base64,' . base64_encode( $svgFile );
		}

		return null;
	}

	/**
	 * Gets file extension by content mime type
	 *
	 * @param string $mime Mime type name to search
	 *
	 * @return ?string
	 */
	public function getExtensionByMime( string $mime ): ?string {
		$extensions = [
			'image/jpeg'    => '.jpeg',
			'image/jpg'     => '.jpg',
			'image/png'     => '.png',
			'image/gif'     => '.gif',
			'image/bmp'     => '.bmp',
			'image/webp'    => '.webp',
			'image/svg+xml' => '.svg',
		];

		return $extensions[ $mime ] ?? null;
	}
}
