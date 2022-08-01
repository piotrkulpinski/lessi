<?php

namespace Piotrkulpinski\Framework\Exception;

use Throwable;
use ReflectionClass;
use ReflectionException;

class Exception extends \Exception {
	public const UNKNOWN_ERROR = 0;

	/**
	 * Exception constructor
	 *
	 * Ensures that the $code parameter is this object's UNKNOWN_ERROR or
	 * on of its children's other constants.
	 *
	 * @param string         $message
	 * @param int|string     $code
	 * @param Throwable|null $previous
	 */
	public function __construct( string $message = '', $code = 0, Throwable $previous = null ) {
		parent::__construct( $message, $this->setCode( $code ), $previous );
	}

	/**
	 * setCode
	 *
	 * Either returns $code unchanged, or sets it to $default if it is not
	 * found within this object's (or one of its children's) constants.
	 *
	 * @param int|string $code
	 * @param int        $default
	 *
	 * @return int
	 */
	protected function setCode( $code, int $default = self::UNKNOWN_ERROR ): int {
		try {
			$reflection  = new ReflectionClass( $this );
			$constants   = $reflection->getConstants();
			$returnValue = ! is_string( $code )

				// if $code is an int, we see if it's in constants.  if so,
				// we can return it directly.  otherwise, we want to return
				// the default.  nested ternary statements are a bad code
				// smell, but this one is pretty straight forward and keeps
				// the code looking good.

				? ( in_array( $code, $constants ) ? $code : $default )

				// otherwise, if $code is a string, we see if it indexes a
				// value in our array of $constants and return that value if it
				// does.  if not, the null coalescing operator will return make
				// sure we return our default.

				: $constants[ $code ] ?? $default;
		} catch ( ReflectionException $exception ) {

			// a ReflectionException is thrown when the class that we're
			// trying to reflect doesn't exist.  but, since we're reflecting
			// this class, we know it exists.  in order to avoid IDE related
			// messages about uncaught exceptions, we'll trigger the following
			// error, but we also know that we should never get here.

			trigger_error( 'Unable to reflect.', E_ERROR );
		}

		// our ReflectionException should never be thrown.  but, because we
		// don't define a return value in the catch block, some IDEs may flag
		// $returnValue as not existing.  so, we'll use the null coalescing
		// operator (again) to make sure our default is returned if, for some
		// reason, we get here.

		return $returnValue ?? $default;
	}
}
