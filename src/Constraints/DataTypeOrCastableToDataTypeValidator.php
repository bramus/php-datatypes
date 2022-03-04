<?php

namespace Bramus\Datatypes\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;


/**
 * This validator check whether a given $value is of a certain Datatype, or whether it can be
 * casted to that certain Datatype. If none of both, it will mark the value as invalid.
 */
class DataTypeOrCastableToDataTypeValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint)
	{
		if (!$constraint instanceof DataTypeOrCastableToDataType) {
			throw new UnexpectedTypeException($constraint, DataTypeOrCastableToDataType::class);
		}

		// Ignore null and empty values to allow other constraints
		// (NotBlank, NotNull, etc.) to take care of that
		if (($value === null) || ($value === '')) {
			return;
		}

		// We only accept arrays and objects
		if (!is_array($value) && !is_object($value)) {
			throw new UnexpectedTypeException($value, 'array|object');
		}

		// It's an instance of datatype: accept
		if (is_a($value, $constraint->datatype)) {
			return;
		}

		// It's an array: check if we can cast it
		if (is_array($value)) {
			try {
				$constraint->datatype::factory($value);
			} catch (\Exception $e) {
				$this->context->buildViolation($constraint->notCastableToExpectedDatatypeMessage)
					->setParameter('{{ expected_datatype }}', $constraint->datatype)
					->setInvalidValue($value)
					->setCode(DataTypeOrCastableToDataType::NOT_CASTABLE_TO_EXPECTED_DATATYPE)
					->addViolation();
			}
			return;
		}

		// It's an object: check if it's an instance of our DataType
		if (!is_a($value, $constraint->datatype)) {
			$this->context->buildViolation($constraint->notAnInstanceOfExpectedDatatypeMessage)
				->setParameter('{{ expected_datatype }}', $constraint->datatype)
				->setParameter('{{ given_datatype }}', get_class($value))
				->setInvalidValue($value)
				->setCode(DataTypeOrCastableToDataType::NOT_AN_INSTANCE_OF_EXPECTED_DATATYPE)
				->addViolation();
		}
	}
}