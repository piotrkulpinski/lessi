<?php

namespace MadeByLess\Lessi\Helper;

use Timber\Timber;
use Timber\Attachment;

/**
 * Provides methods related to media files
 */
trait MediaTrait
{
    /**
     * Returns a custom logo image object
     *
     * @param int $blogId
     *
     * @return ?Attachment
     */
    public function getSiteLogo(int $blogId = 0): ?Attachment
    {
        $switchedBlog = false;

        if (is_multisite() && ! empty($blogId) && get_current_blogId() !== (int) $blogId) {
            switch_to_blog($blogId);
            $switchedBlog = true;
        }

        // We have a logo. Logo is go.
        if ($customLogoId = get_theme_mod('custom_logo')) {
            $image = Timber::get_attachment($customLogoId);
        }

        if ($switchedBlog) {
            restore_current_blog();
        }

        return $image ?? null;
    }

    /**
     * Pulls the image content into the svg markup
     *
     * @param string $path File path
     *
     * @return string
     */
    public function getSvgContent(string $path): string
    {
        if (! empty($path) && $svgFile = @ file_get_contents($path)) {
            $position = strpos($svgFile, '<svg');
            return substr($svgFile, $position);
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
    public function svgToBase64(string $path): ?string
    {
        if (! empty($path) && $svgFile = @ file_get_contents($path)) {
            return 'data:image/svg+xml;base64,' . base64_encode($svgFile);
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
    public function getExtensionByMime(string $mime): ?string
    {
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
