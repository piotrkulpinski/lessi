<?php

namespace MadeByLess\Lessi\Handler;

use Throwable;

/**
 * Class HandlerInterface
 */
interface HandlerInterface
{
    /**
     * Magic methods are always a part of the interface, but this time we
     * need this one, so by declaring it here, PHP will throw a tantrum if
     * it's not defined.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Given stuff, print information about it and then die() if the $die flag is
     * set.  Typically, this only works when the isDebug() method returns true,
     * but the $force parameter will override this behavior.
     *
     * @param mixed $stuff
     * @param bool  $die
     * @param bool  $force
     *
     * @return void
     */
    public function debug($stuff, bool $die = false, bool $force = false): void;

    /**
     * Returns true when WP_DEBUG exists and is set.
     *
     * @return bool
     */
    public function isDebug(): bool;

    /**
     * Calling this method should write $data to the WordPress debug.log file.
     *
     * @param $data
     *
     * @return void
     */
    public function writeLog($data): void;

    /**
     * This serves as a general-purpose Exception handler which displays
     * the caught object when we're debugging and writes it to the log when
     * we're not.
     *
     * @param Throwable $thrown
     *
     * @return void
     */
    public function catcher(Throwable $thrown): void;
}
