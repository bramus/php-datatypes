<?php

namespace Bramus\Datatypes\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\MissingOptionsException;

class EnumerationOrCastableToEnumeration extends Constraint {

	const INVALID_ENUMERATION_IDENTIFIER = '861f550b-f783-4294-b947-4430f5ece233';

	protected static $errorNames = array(
		self::INVALID_ENUMERATION_IDENTIFIER => 'INVALID_ENUMERATION_IDENTIFIER',
	);

    public $invalidEnumerationIdentifierMessage = 'This value is not an valid identifier for the Enumeration {{ expected_enumeration }}.';

    public $enumeration;

    public function __construct($options = null)
    {
        parent::__construct($options);

        if (null === $this->enumeration) {
            throw new MissingOptionsException(sprintf('Option "enumeration" must be given for constraint %s', __CLASS__), array('enumeration'));
        }
    }

}