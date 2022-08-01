<?php

namespace Piotrkulpinski\Framework\Handler;

use Throwable;

/**
 * Class AbstractThemeHandler
 */
abstract class ThemeHandler implements HandlerInterface {

	/**
	 * Checks if the theme was already initialized
	 *
	 * @var bool
	 */
	protected bool $initialized = false;

	/**
	 * Returns the name of this object using the late-static binding so it'll
	 * return the name of the concrete handler, not simply "AbstractThemeHandler".
	 *
	 * @return string
	 */
	public function __toString(): string {
		return static::class;
	}

	/**
	 * Uses addAction and/or addFilter to attach protected methods of this object
	 * to the ecosystem of WordPress action and filter hooks.
	 */
	abstract public function initialize();

	/**
	 * Returns the value of the initialized property at the start of the method
	 * but also sets that value to true.  This function should be called when
	 * initializing handlers if you need to avoid re-initialization problems.
	 *
	 * @return bool
	 */
	final protected function isInitialized(): bool {
		$returnValue       = $this->initialized;
		$this->initialized = true;

		return $returnValue;
	}

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
	public function debug( $stuff, bool $die = false, bool $force = false ): void {
		if ( $this->isDebug() || $force ) {
			$message = '<pre>' . var_export( $stuff, true ) . '</pre>';

			if ( $die ) {
				die( $message );
			}

			echo $message;
		}
	}

	/**
	 * Returns true when WP_DEBUG exists and is set.
	 *
	 * @return bool
	 */
	public function isDebug(): bool {
		return defined( 'WP_DEBUG' ) && WP_DEBUG;
	}

	/**
	 * Calling this method should write $data to the WordPress debug.log file.
	 *
	 * @param mixed $data
	 *
	 * @return void
	 */
	public function writeLog( $data ): void {
		// source:  https://www.elegantthemes.com/blog/tips-tricks/using-the-wordpress-debug-log
		// accessed:  2018-07-09

		if ( ! function_exists( 'write_log' ) ) {
			function write_log( $log ) {
				if ( is_array( $log ) || is_object( $log ) ) {
					error_log( print_r( $log, true ) );
				} else {
					error_log( $log );
				}
			}
		}

		write_log( $data );
	}

	/**
	 * This serves as a general-purpose Exception handler which displays
	 * the caught object when we're debugging and writes it to the log when
	 * we're not.
	 *
	 * @param Throwable $thrown
	 *
	 * @return void
	 */
	public function catcher( Throwable $thrown ): void {
		$this->isDebug() ? $this->debug( $thrown, true ) : $this->writeLog( $thrown );
	}
}
