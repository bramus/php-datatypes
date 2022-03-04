<?php

namespace Bramus\Datatypes\Exception;

class DatatypeCreationException extends \Exception {

	private $errors = [];

	public function __construct($className, $errors) {
		parent::__construct('Could not create new ' . $className . ' instance.');
		$this->errors = $errors;
	}

	public function getErrors() {
		return $this->errors;
	}

}
