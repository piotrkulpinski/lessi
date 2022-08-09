<?php

namespace MadeByLess\Lessi\Helper;

use Exception;
use MadeByLess\Lessi\Helper\HelperTrait;

/**
 * Provides methods to register new hooks in the theme
 */
trait HookTrait
{
    use HelperTrait;

    /**
     * Passes its arguments to add_action().
     *
     * @param string $hook
     * @param mixed  $callback
     * @param int    $priority
     * @param int    $args
     *
     * @throws Exception
     */
    protected function addAction(string $hook, $callback, int $priority = 10, int $args = 1)
    {
        if (! is_callable($callback)) {
            throw new Exception('Callbacks must be a handler method or Closure');
        }

        return is_string($callback)
            ? add_action($hook, [ $this, $callback ], $priority, $args)
            : add_action($hook, $callback, $priority, $args);
    }

    /**
     * Passes its arguments to add_filter.
     *
     * @param string $hook
     * @param mixed  $callback
     * @param int    $priority
     * @param int    $args
     */
    protected function addFilter(string $hook, $callback, int $priority = 10, int $args = 1)
    {
        if (! is_callable($callback)) {
            throw new Exception('Callbacks must be a handler method or Closure');
        }

        return is_string($callback)
            ? add_filter($hook, [ $this, $callback ], $priority, $args)
            : add_filter($hook, $callback, $priority, $args);
    }

    /**
     * Generate proper AJAX hook names and asses its arguments to add_action().
     *
     * @param string $hook
     * @param mixed  $callback
     * @param int    $priority
     * @param int    $args
     */
    protected function addAjaxAction(string $hook, $callback, int $priority = 10, int $args = 1)
    {
        if (! is_callable($callback)) {
            throw new Exception('Callbacks must be a handler method or Closure');
        }

        $this->addAction('wp_ajax_' . $this->buildThemeSlug($hook), $callback, $priority, $args);
        $this->addAction('wp_ajax_nopriv_' . $this->buildThemeSlug($hook), $callback, $priority, $args);

        return true;
    }

    /**
     * Passes its arguments to add_filter and adds a Hook to our collection.
     *
     * @param string $hook
     * @param mixed  $value
     * @param mixed  $args
     *
     * @return mixed
     */
    protected function applyFilter(string $hook, $value, bool $prefix = true, ...$args)
    {
        $hookName = $prefix ? $this->buildThemeSlug($hook) : $hook;

        return apply_filters($hookName, $value, ...$args);
    }
}
