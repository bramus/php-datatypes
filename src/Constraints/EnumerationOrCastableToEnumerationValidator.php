<?php

namespace Bramus\Datatypes\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

/**
 * This validator check whether a given $value is of a certain Datatype, or whether it can be
 * casted to that certain Datatype. If none of both, it will mark the value as invalid.
 */
class EnumerationOrCastableToEnumerationValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint)
	{
		if (!$constraint instanceof EnumerationOrCastableToEnumeration) {
			throw new UnexpectedTypeException($constraint, EnumerationOrCastableToEnumeration::class);
		}

		// Ignore null and empty values to allow other constraints
		// (NotBlank, NotNull, etc.) to take care of that
		if (($value === null) || ($value === '')) {
			return;
		}

		// We only accept scalars and objects
		if (!is_scalar($value) && !is_object($value)) {
			throw new UnexpectedTypeException($value, 'array|object');
		}

		// Make sure the requested enumeration is a subclass of 'Bramus\Enumeration\Enumeration'
		if (!is_subclass_of($constraint->enumeration, 'Bramus\Enumeration\Enumeration')) {
			throw new InvalidArgumentException('Invalid class “' . $constraint->enumeration . '”. It is not a \Bramus\Enumeration\Enumeration subclass.');
		}

		// Value is an instance of the requested enumeration: accept
		if (is_a($value, $constraint->enumeration)) {
			return;
		};

		// Valie is something else: check if it's valid
		if (!$constraint->enumeration::isValidValue($value)) {
			$this->context->buildViolation($constraint->invalidEnumerationIdentifierMessage)
				->setParameter('{{ expected_enumeration }}', $constraint->enumeration)
				->setInvalidValue($value)
				->setCode(EnumerationOrCastableToEnumeration::INVALID_ENUMERATION_IDENTIFIER)
				->addViolation();
		}

		return;
	}
}