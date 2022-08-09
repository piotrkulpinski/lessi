<?php

namespace MadeByLess\Lessi\Factory;

abstract class Singleton
{
    /**
     * @var array The array of Singletons
     */
    private static array $instances = [];

    /**
     * Class constructor.
     */
    protected function __construct()
    {
    }

    /**
     * Insures that only one instance of Singleton exists in memory at any one
     * time. Also prevents needing to define globals all over the place.
     *
     * @return Singleton
     */
    final public static function getInstance()
    {
        $calledClass = get_called_class();

        if (! isset(self::$instances[ $calledClass ]) || ! ( self::$instances[ $calledClass ] instanceof Singleton )) {
            self::$instances[ $calledClass ] = new $calledClass();
        }

        return self::$instances[ $calledClass ];
    }

    /**
     * Throw error on object clone.
     *
     * The whole idea of the singleton design pattern is that there is a single
     * object therefore, we don't want the object to be cloned.
     *
     * @return void
     */
    public function __clone()
    {
        // Cloning instances of the class is forbidden.
        trigger_error('Cheatin&#8217; huh?');
    }

    /**
     * Disable unserializing of the class.
     *
     * @return void
     */
    public function __wakeup()
    {
        // Unserializing instances of the class is forbidden.
        trigger_error('Cheatin&#8217; huh?');
    }
}
