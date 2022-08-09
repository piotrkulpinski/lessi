<?php

namespace MadeByLess\Lessi\Helper;

use Timber\URLHelper;
use MadeByLess\Lessi\Helper\AssetTrait;

/**
 * Provides methods to enqueue/dequeue static files
 */
trait EnqueueTrait
{
    use AssetTrait;

    /**
     * Adds style file after making sure it exists
     *
     * @param string $handle
     * @param string $src
     *
     * @return void
     */
    protected function addStyle(string $handle, string $src = '', ...$args): void
    {
        $this->enqueue('style', $handle, $src, ...$args);
    }

    /**
     * Adds script file after making sure it exists
     *
     * @param string $handle
     * @param string $src
     *
     * @return void
     */
    protected function addScript(string $handle, string $src = '', ...$args): void
    {
        $this->enqueue('script', $handle, $src, ...$args);
    }

    /**
     * Adds inline style file
     *
     * @param string $handle
     * @param string $data
     *
     * @return void
     */
    protected function addInlineStyle(string $handle, string $data): void
    {
        wp_add_inline_style($handle, $data);
    }

    /**
     * Adds inline script file
     *
     * @param string $handle
     * @param string $src
     *
     * @return void
     */
    protected function addInlineScript(string $handle, string $src): void
    {
        wp_add_inline_script($handle, $src);
    }

    /**
     * Removes style file
     *
     * @param string $handle
     *
     * @return void
     */
    protected function removeStyle(string $handle): void
    {
        wp_dequeue_style($handle);
    }

    /**
     * Removes script file
     *
     * @param string $handle
     *
     * @return void
     */
    protected function removeScript(string $handle): void
    {
        wp_dequeue_script($handle);
    }

    /**
     * Determines whether a file path is absolute or not and enqueues it
     *
     * @param string $type
     * @param string $handle
     * @param string $src
     *
     * @return void
     */
    private function enqueue($type, $handle, $src = '', ...$args)
    {
        $function = "wp_enqueue_$type";

        if (URLHelper::is_url($src)) {
            $function($handle, $src, ...$args);
        } elseif ($this->hasFile($src)) {
            $function($handle, $this->revisionedUrl($src), ...$args);
        }
    }
}
