<?php

namespace Bramus\Datatypes;

use Bramus\Datatypes\Helpers\Validator;
use Bramus\Datatypes\Helpers\Builder;

use Bramus\Datatypes\Exception\DatatypeCreationException;

abstract class BaseDatatype {

	public function validateData($data = []) {
		return Validator::validateData(get_called_class(), $data);
	}

	public static function factory($data = []) {
		return Builder::createInstance(get_called_class(), $data);
	}

	public static function partial($data) {
		return Builder::createPartialInstance(get_called_class(), $data);
	}

	public static function isBaseDatatypeOrInstanceOf($object, $class = null) {
		return is_a($object, $class ?: get_called_class());
	}

	abstract public function toDataValue();

	public static function getValidationConstraints($data = []) {
		throw new \Exception('getValidationConstraints not implemented in ' . get_called_class(), 1);
	}

}
