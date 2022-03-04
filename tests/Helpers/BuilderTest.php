<?php

namespace Tests\Bramus\Datatypes\Helpers;

use \Bramus\Datatypes\Helpers\Builder;
use \Bramus\Datatypes\Exception\DatatypeCreationException;
use \PHPUnit\Framework\TestCase;
use \Tests\Bramus\Datatypes\SampleDatatype;

class BuilderTest extends TestCase
{

    public function testCreateInstanceShouldWork()
    {
        $entry = [
            'language' => 'nl',
            'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
        ];

        $instance = Builder::createInstance(SampleDatatype::class, $entry);

        $this->assertInstanceOf(SampleDatatype::class, $instance);
        $this->assertEquals($entry['language'], $instance->getLanguage());
        $this->assertEquals($entry['text'], $instance->getText());
    }

    public function testCreateInstanceWithPartialDataShouldThrowAnException()
    {
        $entry = [
            'language' => 'nl',
        ];

		$this->expectException(DatatypeCreationException::class);
		$instance = Builder::createInstance(SampleDatatype::class, $entry);
    }

    public function testPartialCreateInstanceShouldWork()
    {
        $entry = [
            'language' => 'nl',
        ];

        $instance = Builder::createPartialInstance(SampleDatatype::class, $entry);

        $this->assertInstanceOf(SampleDatatype::class, $instance);
        $this->assertEquals($entry['language'], $instance->getLanguage());
        $this->assertEquals(null, $instance->getText());
    }

    public function testPartialCreateInstanceWithFaultyDataShouldThrowAnException()
    {
        $entry = [
            'language' => 'nl_nl', // This is too long
        ];

        $this->expectException(DatatypeCreationException::class);
        $instance = Builder::createPartialInstance(SampleDatatype::class, $entry);
    }

}