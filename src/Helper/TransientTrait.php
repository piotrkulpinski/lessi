<?php

namespace MadeByLess\Lessi\Helper;

use MadeByLess\Lessi\Helper\HelperTrait;

/**
 * Provides methods to run file-related tasks
 */
trait TransientTrait
{
    use HelperTrait;

    /**
     * Retrieves the value of a transient.
     *
     * @param string $transient Transient name.
     *
     * @return mixed Value of transient
     */
    protected function getTransient(string $transient)
    {
        return get_transient($this->buildThemeSlug($transient));
    }

    /**
     * Sets/updates the value of a transient.
     *
     * @param string $transient Transient name.
     * @param mixed  $value Transient value. Must be serializable if non-scalar.
     * @param int    $expiration Time until expiration in seconds. Default 0 (no expiration).
     *
     * @return bool True if the value was set, false otherwise.
     */
    protected function setTransient(string $transient, $value, int $expiration = 0): bool
    {
        return set_transient($this->buildThemeSlug($transient), $value, $expiration);
    }

    /**
     * Removes the transient completely.
     *
     * @param string $transient Transient name.
     *
     * @return bool True if the value was deleted, false otherwise.
     */
    protected function deleteTransient(string $transient): bool
    {
        return delete_transient($this->buildThemeSlug($transient));
    }
}
