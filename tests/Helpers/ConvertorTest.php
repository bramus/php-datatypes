<?php

namespace Tests\Bramus\Datatypes\Helpers;

use \Bramus\Datatypes\Exception\DatatypeCreationException;
use \PHPUnit\Framework\TestCase;

class ConvertorTest extends TestCase
{

	public function testSingleToDataType()
	{
		$entry = [
			'language' => 'nl',
			'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
		];

		$instance = \Bramus\Datatypes\Helpers\Convertor::singleToDatatype($entry, \Tests\Bramus\Datatypes\SampleDatatype::class);

		$this->assertInstanceOf(\Tests\Bramus\Datatypes\SampleDatatype::class, $instance);
		$this->assertEquals('nl', $instance->getLanguage());
	}

	public function testSingleToDataTypeWithInstance()
	{
		$instance = \Tests\Bramus\Datatypes\SampleDatatype::factory([
			'language' => 'nl',
			'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
		]);

		$instance2 = \Bramus\Datatypes\Helpers\Convertor::singleToDatatype($instance, \Tests\Bramus\Datatypes\SampleDatatype::class);

		$this->assertEquals($instance, $instance2);
	}

	public function testSingleToDataTypeWithFaultyData()
	{
		$entry = [
			'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
		];

		$this->expectException(DatatypeCreationException::class);
		$instance = \Bramus\Datatypes\Helpers\Convertor::singleToDatatype($entry, \Tests\Bramus\Datatypes\SampleDatatype::class);
	}

	public function testSingleToDataTypeWithFaultyClass()
	{
		$entry = [
			'language' => 'nl',
			'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
		];

		$this->expectException(DatatypeCreationException::class);
		$instance = \Bramus\Datatypes\Helpers\Convertor::singleToDatatype($entry, \DateTime::class);
	}

	public function testMultipleToDataType()
	{
		$entries = [
			[
				'language' => 'nl',
				'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
			],
			[
				'language' => 'fr',
				'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
			],
		];

		$instances = \Bramus\Datatypes\Helpers\Convertor::multipleToDatatype($entries, \Tests\Bramus\Datatypes\SampleDatatype::class);

		$this->assertInstanceOf(\Tests\Bramus\Datatypes\SampleDatatype::class, $instances[0]);
		$this->assertEquals('nl', $instances[0]->getLanguage());
		$this->assertInstanceOf(\Tests\Bramus\Datatypes\SampleDatatype::class, $instances[1]);
		$this->assertEquals('fr', $instances[1]->getLanguage());
	}

	public function testMultipleToDataTypeWithInstances()
	{
		$instances = [
			\Tests\Bramus\Datatypes\SampleDatatype::factory([
				'language' => 'nl',
				'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
			]),
				\Tests\Bramus\Datatypes\SampleDatatype::factory([
				'language' => 'fr',
				'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
			]),
		];

		$instances2 = \Bramus\Datatypes\Helpers\Convertor::multipleToDatatype($instances, \Tests\Bramus\Datatypes\SampleDatatype::class);

		$this->assertEquals($instances[0], $instances2[0]);
		$this->assertEquals($instances[1], $instances2[1]);
	}

	public function testMultipleToDataTypeWithMixedData()
	{
		$mixedData = [
			[
				'language' => 'nl',
				'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
			],
			\Tests\Bramus\Datatypes\SampleDatatype::factory([
				'language' => 'fr',
				'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
			]),
		];

		$instances = \Bramus\Datatypes\Helpers\Convertor::multipleToDatatype($mixedData, \Tests\Bramus\Datatypes\SampleDatatype::class);

		$this->assertInstanceOf(\Tests\Bramus\Datatypes\SampleDatatype::class, $instances[0]);
		$this->assertEquals('nl', $instances[0]->getLanguage());
		$this->assertInstanceOf(\Tests\Bramus\Datatypes\SampleDatatype::class, $instances[1]);
		$this->assertEquals('fr', $instances[1]->getLanguage());
	}

	public function testMultipleToDataTypeWithFaultyData()
	{
		$entries = [
			[
				'language' => 'nl',
				'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
			],
			[
				'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
			],
		];

		$this->expectException(DatatypeCreationException::class);
		$instances = \Bramus\Datatypes\Helpers\Convertor::multipleToDatatype($entries, \Tests\Bramus\Datatypes\SampleDatatype::class);
	}

	public function testMultipleToDataTypeWithFaultyClass()
	{
		$entries = [
			[
				'language' => 'nl',
				'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
			],
			[
				'language' => 'fr',
				'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
			],
		];

		$this->expectException(DatatypeCreationException::class);
		$instances = \Bramus\Datatypes\Helpers\Convertor::multipleToDatatype($entries, \DateTime::class);
	}

	public function testSingleToDatavalue()
	{
		$instance = \Tests\Bramus\Datatypes\SampleDatatype::factory([
			'language' => 'nl',
			'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
		]);

		$entry = \Bramus\Datatypes\Helpers\Convertor::singleToDatavalue($instance);

		$this->assertIsArray($entry);
		$this->assertEquals('nl', $entry['language']);
	}

	public function testSingleToDatavalueWithFaultyInstance()
	{
		// Not a BaseDatatype
		$instance = new \DateTime('now');

		$this->expectException(DatatypeCreationException::class);
		$instances = \Bramus\Datatypes\Helpers\Convertor::singleToDatavalue($instance);
	}

	public function testMultipleToDatavalue()
	{
		$instances = [
			\Tests\Bramus\Datatypes\SampleDatatype::factory([
				'language' => 'nl',
				'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
			]),
			\Tests\Bramus\Datatypes\SampleDatatype::factory([
				'language' => 'fr',
				'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
			]),
		];

		$entries= \Bramus\Datatypes\Helpers\Convertor::multipleToDatavalue($instances);

		$this->assertIsArray($entries[0]);
		$this->assertEquals('nl', $entries[0]['language']);
		$this->assertIsArray($entries[1]);
		$this->assertEquals('fr', $entries[1]['language']);
	}

	public function testMultipleToDatavalueWithFaultyInstance()
	{
		$instances = [
			\Tests\Bramus\Datatypes\SampleDatatype::factory([
				'language' => 'nl',
				'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
			]),
			new \DateTime('now'),
		];

		$this->expectException(DatatypeCreationException::class);
		$instances = \Bramus\Datatypes\Helpers\Convertor::multipleToDatavalue($instances);
	}

}
