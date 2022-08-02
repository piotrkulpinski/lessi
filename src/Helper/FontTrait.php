<?php

namespace MadeByLess\Lessi\Helper;

use MadeByLess\Lessi\Helper\TransientTrait;

/**
 * Provides methods for getting and manipulating fonts
 */
trait FontTrait {
	use TransientTrait;

	/**
	 * Get Google fonts list
	 *
	 * @param string $apiKey Google Fonts API Key
	 */
	protected function getGoogleFonts( string $apiKey ) {
		if ( ! is_customize_preview() ) {
			return [];
		}

		if ( ! $googleFonts = $this->getTransient( 'google_fonts' ) ) {
			$googleFonts = $this->fetchGoogleFonts( $apiKey ) ?? [];
			$googleFonts = array_column( $googleFonts, 'family' );

			$this->setTransient( 'google_fonts', $googleFonts, WEEK_IN_SECONDS );
		}

		return array_combine( $googleFonts, $googleFonts );
	}

	/**
	 * Fetches fonts from Google Fonts API
	 *
	 * @param string $apiKey Google Fonts API Key
	 * @param string $sort   Optional. Sort option to pass to the Google Fonts API
	 *
	 * @return array|null
	 */
	protected function fetchGoogleFonts( string $apiKey, string $sort = 'popularity' ): ?array {
		$ch = curl_init( "https://www.googleapis.com/webfonts/v1/webfonts?key=$apiKey&sort=$sort" );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, [ 'Content-Type: application/json' ] );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );

		$response = curl_exec( $ch );
		$httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		curl_close( $ch );

		$fonts = json_decode( $response, true );

		if ( $httpCode === 200 && ! empty( $fonts ) ) {
			return $fonts['items'];
		}

		return null;
	}

	/**
	 * Parses Google Fonts url
	 *
	 * @param array $fonts Array of Google Font names
	 *
	 * @return string|null
	 */
	protected function getGoogleFontsUrl( array $fonts ): ?string {
		if ( empty( $fonts ) ) {
			return null;
		}

		$fontFamilies = [];

		foreach ( $fonts as $font ) {
			if ( ! array_key_exists( $font, $fontFamilies ) ) {
				$fontFamilies[ $font ] = "{$font}:400,700";
			}
		}

		$args = [
			'family' => urlencode( implode( '|', array_values( $fontFamilies ) ) ),
			'subset' => urlencode( 'latin,latin-ext' ),
		];

		return add_query_arg( $args, 'https://fonts.googleapis.com/css' );
	}
}
