<?php

namespace Piotrkulpinski\Framework\Helper;

use Piotrkulpinski\Framework\Helper\FileTrait;

/**
 * Provides methods to manipulate static asset files
 */
trait AssetTrait {

	use FileTrait;

	/**
	 * Stored manifest JSON file
	 *
	 * @var array
	 */
	private $manifest;

	/**
	 * Returns the real path of the asset file.
	 *
	 * @param $asset
	 *
	 * @return string
	 */
	public function assetUrl( $asset ) {
		return $this->revisionedUrl( $this->getPath( $this->config->getAssetsPath(), $asset ) );
	}

	/**
	 * Verifies existence of the given file in manifest
	 *
	 * @return bool
	 */
	protected function hasFile( $asset ) {
		$manifest = $this->getManifest();

		return array_key_exists( $asset, $manifest );
	}

	/**
	 * Returns the real url of the revisioned file.
	 * based on the manifest file content.
	 *
	 * @param $asset
	 *
	 * @return string
	 */
	protected function revisionedUrl( $asset ) {
		$manifest = $this->getManifest();

		if ( ! array_key_exists( $asset, $manifest ) ) {
			return 'FILE-NOT-REVISIONED';
		}

		return $this->getTemplateUrl( $this->config->getDistPath(), $manifest[ $asset ] );
	}

	/**
	 * Checks if request is in development environment
	 *
	 * @return boolean
	 */
	private function isDev() {
		return defined( 'THEME_DEV_ENV' );
	}

	/**
	 * Get parsed manifest file content
	 *
	 * @return array
	 */
	private function getManifest() {
		if ( empty( $this->manifest ) ) {
			$this->manifest = $this->fetchManifest();
		}

		return $this->manifest;
	}

	/**
	 * Fetches data from remote manifest file
	 */
	private function fetchManifest() {
		$manifestPath = $this->isDev()
			? $this->config->getManifestDevPath()
			: $this->config->getManifestPath();

		$response = wp_remote_get( $this->getTemplateUrl( $manifestPath ) );

		if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
			return (array) json_decode( $response['body'] );
		}
	}
}
