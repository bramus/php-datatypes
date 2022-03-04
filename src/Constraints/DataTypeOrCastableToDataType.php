<?php

namespace Bramus\Datatypes\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\MissingOptionsException;

class DataTypeOrCastableToDataType extends Constraint {

	const NOT_CASTABLE_TO_EXPECTED_DATATYPE = '61b14c02-ae41-43bb-8725-ea3cedbd88a9';
	const NOT_AN_INSTANCE_OF_EXPECTED_DATATYPE = '75438741-4399-4412-8c9d-e5508f470dc5';

	protected static $errorNames = array(
		self::NOT_CASTABLE_TO_EXPECTED_DATATYPE => 'NOT_CASTABLE_TO_EXPECTED_DATATYPE',
		self::NOT_AN_INSTANCE_OF_EXPECTED_DATATYPE => 'NOT_AN_INSTANCE_OF_EXPECTED_DATATYPE',
	);

    public $notCastableToExpectedDatatypeMessage = 'This array was not castable to the type {{ expected_datatype }}.';
    public $notAnInstanceOfExpectedDatatypeMessage = 'This value should be a datatype of the type {{ expected_datatype }}. A {{ given_datatype }} was given.';

    public $datatype;

    public function __construct($options = null)
    {
        parent::__construct($options);

        if (null === $this->datatype) {
            throw new MissingOptionsException(sprintf('Option "datatype" must be given for constraint %s', __CLASS__), array('datatype'));
        }
    }

}