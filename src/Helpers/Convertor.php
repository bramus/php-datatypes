<?php

namespace Bramus\Datatypes\Helpers;

use Bramus\Datatypes\Exception\DatatypeCreationException;

class Convertor {

	public static function singleToDatatype($entry, $className)
	{
		// We only suppport datatypes as target classes
		if (!is_subclass_of($className, 'Bramus\Datatypes\BaseDatatype')) {
			throw new DatatypeCreationException(get_called_class(), 'Could not cast the contents of […] to a ' . $className .' Datatype instance, as ' . $className . ' not a \Bramus\Datatypes\BaseDatatype subclass.');
		}

		// Already a $className instance:
		// ~> Just return it
		if (is_a($entry, $className)) {
			return $entry;
		}

		// Not a $className instance:
		// ~> Try to cast it
		try {
			$entry = $className::factory($entry);
			return $entry;
		} catch (\Exception $e) {
			throw new DatatypeCreationException($className, 'Could not cast the contents of […] to a ' . $className . ' Datatype instance.');
		}

	}

	public static function multipleToDatatype(array $data, $className)
	{
		return array_map(function($entry) use ($className) {
			return self::singleToDatatype($entry, $className);
		}, $data);
	}

	public static function singleToDatavalue($datatype)
	{
		if (!is_a($datatype, \Bramus\Datatypes\BaseDatatype::class))
		{
			throw new DatatypeCreationException(get_called_class(), 'Could not get the dataValue of […] as it\'s not a \Bramus\Datatypes\BaseDatatype instance.');
		}

		return $datatype->toDataValue();
	}

	public static function multipleToDatavalue($datatypes)
	{
		return array_map(function($datatype) {
			return self::singleToDatavalue($datatype);
		}, $datatypes);
	}

}