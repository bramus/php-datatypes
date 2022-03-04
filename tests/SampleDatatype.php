<?php

namespace Tests\Bramus\Datatypes;

use \Bramus\Datatypes\BaseDatatype;
use \Bramus\Datatypes\Exception\DatatypeCreationException;

use \Symfony\Component\Validator\Constraints;
use \Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Sample Datatype (OCPI's DisplayText)
 */
class SampleDatatype extends BaseDatatype {

	private $language;
	private $text;

	/**
	 * [__construct description]
	 * @param [type] $allowed [description]
	 */
	public function __construct($language, $text) {
		$this->validateData(get_defined_vars());

		$this->language = $language;
		$this->text = $text;
	}

	public static function getValidationConstraints($data = []) {

		// Don't validate instances of ourselves. If they exist, we assume they are valid.
		if (self::isBaseDatatypeOrInstanceOf($data)) {
			return [];
		};

		$data = (array) $data;

		// Optional Validations
		$optionalValidations = [];

		// Required Validations
		$requiredValidations = array(
			'language' => array(
				new Constraints\NotBlank(),
				new Constraints\Length(array(
					'max' => 2,
				)),
			),
			'text' => array(
				new Constraints\NotBlank(),
				new Constraints\Length(array(
					'max' => 512,
				)),
			),
		);

		$constraints = new Constraints\Collection(
			$optionalValidations + $requiredValidations
		);

		return $constraints;

	}

	public function toDataValue() {
		$toReturn = [
			'language' => $this->getLanguage(),
			'text' => $this->getText(),
		];

		return $toReturn;
	}

	public function getLanguage() {
		return (string) $this->language;
	}

	public function getText() {
		return (string) $this->text;
	}

}