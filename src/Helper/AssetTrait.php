<?php

namespace MadeByLess\Lessi\Helper;

use MadeByLess\Lessi\Helper\FileTrait;

/**
 * Provides methods to manipulate static asset files
 */
trait AssetTrait
{
    use FileTrait;

    /**
     * Stored manifest JSON file
     *
     * @var array|null
     */
    private ?array $manifest;

    /**
     * Returns the real path of the asset file.
     *
     * @param string $asset Path to the asset file
     *
     * @return string
     */
    public function assetUrl(string $asset): string
    {
        return $this->revisionedUrl($asset);
    }

    /**
     * Verifies existence of the given file in manifest
     *
     * @param string $asset Path to the asset file
     * @param array|null $manifest Optional. Already fetched manifest object
     *
     * @return bool
     */
    protected function hasFile(string $asset, ?array $manifest = null): bool
    {
        $manifest ??= $this->getManifest();

        return is_array($manifest) && array_key_exists($asset, $manifest);
    }

    /**
     * Returns the real url of the revisioned file.
     * based on the manifest file content.
     *
     * @param string $asset Path to the asset file
     *
     * @return string
     */
    protected function revisionedUrl(string $asset): string
    {
        $manifest = $this->getManifest();

        if (! $this->hasFile($asset, $manifest)) {
            return 'FILE-NOT-REVISIONED';
        }

        return $this->getTemplateUrl(config()->getPublicPath(), $manifest[ $asset ]);
    }

    /**
     * Get parsed manifest file content
     *
     * @return array
     */
    private function getManifest(): array
    {
        if (empty($this->manifest)) {
            $this->manifest = $this->fetchManifest();
        }

        return $this->manifest;
    }

    /**
     * Fetches data from local manifest file
     *
     * @return array
     */
    private function fetchManifest(): array
    {
        $path = $this->getTemplatePath(
            config()->getPublicPath(),
            config()->getManifestPath()
        );

        return file_exists($path) ? json_decode(file_get_contents($path), true) : [];
    }
}
