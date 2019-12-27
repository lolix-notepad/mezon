<?php

class FieldTest extends PHPUnit\Framework\TestCase
{

	/**
	 * Testing constructor
	 */
	public function testNoNameException()
	{
		try {
			// test body
			$Field = new \Mezon\GUI\Field([],'');

			$this->fail('Exception was not thrown ' . serialize($Field));
		} catch (Exception $e) {
			$this->addToAssertionCount(1);
		}
	}

	/**
	 * Testing setters
	 */
	public function testNameSetter()
	{
		// test body
	    $Field = new \Mezon\GUI\Field(json_decode(file_get_contents(__DIR__ . '/conf/name-setter.json'), true),'');

		// assertions
		$this->assertContains('prefixfield-name000', $Field->html(), 'Invalid field "name" value');
	}

	/**
	 * Testing setters
	 */
	public function testRequiredSetter()
	{
		// test body
	    $Field = new \Mezon\GUI\Field(json_decode(file_get_contents(__DIR__ . '/conf/required-setter.json'), true),'');

		// assertions
		$this->assertContains('prefixfield-name1111select2', $Field->html(), 'Invalid field "name" value');
	}
}

?>