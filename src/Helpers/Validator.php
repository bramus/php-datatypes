<?php

namespace Bramus\Datatypes\Helpers;

use Symfony\Component\Validator\Validation;
use Bramus\Datatypes\Exception\DatatypeCreationException;

class Validator {

	public static function reworkErrors($errors = []) {
		$reworkedErrors = [];
		if (!is_array($errors)) {
			$errors = [$errors];
		}
		foreach ($errors as $error) {
			// @TODO: Check if it's a Symfony Error instead of this simple check …
			if (!is_string($error)) {
				$reworkedErrors[str_replace('][', '.', substr(substr($error->getPropertyPath(), 1), 0, -1))] = $error->getMessage();
			} else {
				$reworkedErrors[] = $error;
			}
		}
		return $reworkedErrors;
	}

	public static function validateData($className, $data) {

 		// Remove nulls and empty strings from our data
 		// so that our validator only validates the required fields
 		$data = self::cleanupData($data);

		// @TODO: Check how this one behaves compared to https://github.com/silexphp/Silex-Providers/blob/master/ValidatorServiceProvider.php
		try {
			$validator = Validation::createValidator();
			$constraints = $className::getValidationConstraints($data);

			if (Builder::$partializerEnabled) {
				$constraints->allowMissingFields = true;
			}

			$validationResultList = $validator->validate($data, $constraints);
			if ($validationResultList->count() > 0) {
				$errors = [];

				foreach ($validationResultList as $validationResult) {
					$errors[] = $validationResult;
				}

				throw new DatatypeCreationException($className, self::reworkErrors($errors));
			}
			return true;
		}

		// The Symfony Validator has crashed. This can be, for example,
		// - When performing a Count() on a non-array
		// - Using a foreach in a Callback on non-arrays
		// - …
		catch (\Exception $e) {
			if (is_a($e, DatatypeCreationException::class)) {
				throw $e;
			}
			// echo $e->getMessage() . PHP_EOL;
			// echo $e->getTraceAsString();
			throw new DatatypeCreationException($className, $e->getMessage());
		}
	}

	public static function cleanupData($data, $valuesToRemove = ['', null]) {
		// We can only clean up arrays …
		if (!is_array($data))
		{
			return $data;
		}

		foreach ($data as $key => &$value) {
			if (is_array($value)) {
				$value = self::cleanupData($value);
			} else if (in_array($value, $valuesToRemove, true)) {
				unset($data[$key]);
			}
		}

		return $data;
	}

}