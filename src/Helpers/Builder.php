<?php

namespace Bramus\Datatypes\Helpers;

use Bramus\Datatypes\Exception\DatatypeCreationException;

class Builder {

	public static $partializerEnabled = false;

	public static function createInstance($className, $data = [])
	{
		// Don't do double work: if it already is an instance, then return it immediately
		if (is_a($data, $className)) {
			return $data;
		}

		// Extract the constructor from the given $className
		$reflector = new \ReflectionClass($className);
 		$constructor = $reflector->getConstructor();

 		// No constructor defined for $className?
 		// ~> Return a blank instance …
 		if(is_null($constructor)) {
 			return new $className;
 		}

 		// The passed in $data is not an array?
 		// ~> Use $data as the (single) argument for the $className
 		if (!is_array($data)) {
 			try {
				$instanceArgs = [$data];
				// Apparently the try-catch around this does not catch Exceptions where too few parameters are passed into the constructor,
				// so we have to detect this ourselves …
				if (sizeof($instanceArgs) < $constructor->getNumberOfRequiredParameters()) {
					throw new DatatypeCreationException($className, 'Can not create new instance of ' . $className . '. Too few arguments are given.');
				}
				$instance =  $reflector->newInstanceArgs($instanceArgs);
				return $instance;
	 		} catch (\Exception $e) {
	 			throw new DatatypeCreationException($className, $e->getMessage());
	 		}
 		}

 		// Build instance arguments by
 		// - getting all parameters for the constructors
 		// - injecting the values from $data as the value for the parameters (or fallback to the default one if defined)
 		$instanceArgs = [];
		foreach ($constructor->getParameters() as $param)
		{

			$expectedClass = $param->getClass();
			$expectedType = $param->getType();

			// No value set in $data
			// ~> Use the default value (if any) or NULL
			if (!isset($data[$param->getName()])) {
				if ($param->isDefaultValueAvailable()) {
					$instanceArg = $param->getDefaultValue();

					// Play nice with Bramus\Enumeration and try and cast the
					// default value to an actual instance because most likely
					// a simple value (instead of an instance) was defined.
					if ($expectedClass && is_subclass_of($expectedClass->getName(), \Bramus\Enumeration\Enumeration::class) && !is_a($instanceArg, $expectedClass->getName())) {
						if ($expectedClass->getName()::isValidValue($instanceArg)) {
							$expectedClassName = $expectedClass->getName();
							$instanceArg = new $expectedClassName($instanceArg);
						} else {
							throw new DatatypeCreationException($expectedClass->getName(), 'Invalid default value for '. $param->getName() .'. It should be casted to a ' . $expectedClass . ' instance, but the value is not valid for it.');
						}
					}

					$instanceArgs[] = $instanceArg;
				} elseif ($expectedClass) {
					// @TODO: When building a Partialized instance **AND** a class is expected, don't throw an exception but do something else …
					throw new DatatypeCreationException($expectedClass->getName(), 'Could not set value for argument “' . $param->getName() . '” as it\'s missing');
				} else {
					$instanceArgs[] = null;
				}
			}

			// Value set in $data
			else {
				$value = $data[$param->getName()];

				// We expect a class
				// ~> Use the builder to make sure it's an instance of said class (auto-casting, yay!)
				if ($expectedClass) {
					$instanceArgs[] = self::createInstance($expectedClass->getName(), $value);
				}

				// We expect a type
				// ~> Make sure the param is of said type, or abort (but do allow nulls in case the argument allows it)
				elseif ($expectedType && (getType($value) != $expectedType->getName())) {
					// Allow null though, in case param allows it
					if ($expectedType->allowsNull() && $value === null) {
						$instanceArgs[] = $value;
					} else {
						throw new DatatypeCreationException($className, 'Invalid value for argument “' . $param->getName() . '”. Expected “' . $expectedType->getName() . '”, got “' . getType($value) . '” instead.');
					}
				}

				// We don't expect a class
				// ~> Use the value directly
				else {
					$instanceArgs[] = $value;
				}
			}
		}

		// Create new class instance with given arguments
		try {
 			return $reflector->newInstanceArgs($instanceArgs);
 		} catch (\Exception $e) {
			if (is_a($e, DatatypeCreationException::class)) {
				throw $e;
			}
 			throw new DatatypeCreationException($className, $e->getMessage());
 		}
	}

	public static function createPartialInstance($className, $data = [])
	{
		self::$partializerEnabled = true;

		try {
			$instance = self::createInstance($className, $data);
		} catch (\Exception $e) {
			self::$partializerEnabled = false;
			throw $e;
		}

		self::$partializerEnabled = false;
		return $instance;
	}

}