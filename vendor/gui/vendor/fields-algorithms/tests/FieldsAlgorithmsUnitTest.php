<?php
require_once (__DIR__ . '/../fields-algorithms.php');
require_once (__DIR__ . '/../../../../functional/functional.php');

define('ENTITY_NAME', 'entity');
define('ID_FIELD_NAME', 'id');
define('TITLE_FIELD_NAME', 'title');
define('USER_ID_FIELD_NAME', 'user_id');
define('FIELDS_FIELD_NAME', 'fields');
define('DISABLED_FIELD_NAME', 'disabled');
define('STRING_TYPE_NAME', 'string');
define('INTEGER_TYPE_NAME', 'integer');
define('DATE_TYPE_NAME', 'date');
define('EXTERNAL_TYPE_NAME', 'external');

class FieldsAlgorithmsUnitTest extends PHPUnit\Framework\TestCase
{

    /**
     * Post test processing
     */
    public function tearDown():void
    {
        unset($_GET[FIELDS_FIELD_NAME]);
    }

    /**
     * Method creates testing data
     *
     * @return array Testing data
     */
    protected function get_fields_1(): array
    {
        return (json_decode(file_get_contents(__DIR__ . '/conf/setup.json'), true));
    }

    /**
     * Method creates testing data
     *
     * @return array Testing data
     */
    protected function get_fields_2(): array
    {
        return ([
            ID_FIELD_NAME => [
                'type' => INTEGER_TYPE_NAME,
                DISABLED_FIELD_NAME => 1
            ],
            TITLE_FIELD_NAME => [
                'type' => STRING_TYPE_NAME,
                'required' => 1
            ]
        ]);
    }

    /**
     * Testing invalid construction
     */
    public function test_constructor()
    {
        // setup and test body
        $FieldsAlgorithms = new \Mezon\GUI\FieldsAlgorithms($this->get_fields_1(), ENTITY_NAME);

        // assertions
        $this->assertEquals(ENTITY_NAME, $FieldsAlgorithms->EntityName, 'EntityName was not set');
        $this->assertTrue($FieldsAlgorithms->has_custom_fields(), 'Data was not loaded');
    }

    /**
     * Testing has_custom_fields
     */
    public function test_has_not_custom_fields()
    {
        // setup and test body
        $FieldsAlgorithms = new \Mezon\GUI\FieldsAlgorithms($this->get_fields_2(), ENTITY_NAME);

        // assertions
        $_GET[FIELDS_FIELD_NAME] = TITLE_FIELD_NAME;
        $this->assertFalse($FieldsAlgorithms->has_custom_fields(), 'Custom fields are not in the model');
    }

    /**
     * Testing has_custom_fields
     */
    public function test_has_custom_fields()
    {
        // setup and test body
        $FieldsAlgorithms = new \Mezon\GUI\FieldsAlgorithms($this->get_fields_1(), ENTITY_NAME);

        // assertions
        $this->assertTrue($FieldsAlgorithms->has_custom_fields(), 'Custom fields are in the model');
    }

    /**
     * Testing get_typed_value
     */
    public function test_get_typed_value()
    {
        // setup and test body
        $FieldsAlgorithms = new \Mezon\GUI\FieldsAlgorithms($this->get_fields_1(), ENTITY_NAME);

        // assertions int
        $this->assertEquals(1, $FieldsAlgorithms->get_typed_value(INTEGER_TYPE_NAME, '1'), 'Type was not casted properly for integer');
        $this->assertTrue(is_int($FieldsAlgorithms->get_typed_value(INTEGER_TYPE_NAME, '1')), 'Type was not casted properly for integer');

        // assertions string
        $this->assertEquals('1', $FieldsAlgorithms->get_typed_value(STRING_TYPE_NAME, '1'), 'Type was not casted properly for string');
        $this->assertTrue(is_string($FieldsAlgorithms->get_typed_value(STRING_TYPE_NAME, '1')), 'Return type is not correct');
        $this->assertEquals('&amp;', $FieldsAlgorithms->get_typed_value(STRING_TYPE_NAME, '&'), 'Type was not casted properly for string');
        $this->assertEquals('', $FieldsAlgorithms->get_typed_value(STRING_TYPE_NAME, '""'), 'Default brunch for string is not working');

        // assertions date
        $this->assertEquals('2019-01-01', $FieldsAlgorithms->get_typed_value(DATE_TYPE_NAME, '2019-01-01'), 'Type was not casted properly for date');
        $this->assertEquals('', $FieldsAlgorithms->get_typed_value(DATE_TYPE_NAME, '""'), 'Default date for string is not working');

        // assertions file
        $this->assertArraySubset([
            'value'
        ], $FieldsAlgorithms->get_typed_value('file', [
            'value'
        ], false), 'Type was returned properly');
        $this->assertFileExists($Path = $FieldsAlgorithms->get_typed_value('file', [
            'name' => 'test.txt',
            'file' => '1234'
        ], true), 'File was not saved');
        unlink($Path);

        // assertions external
        $this->assertArraySubset([
            1,
            2
        ], $FieldsAlgorithms->get_typed_value(EXTERNAL_TYPE_NAME, [
            '1',
            '2'
        ]), true, 'Type was not casted properly');

        // assertion unexisting
        try {
            $FieldsAlgorithms->get_typed_value('unexisting', '1');
            $this->fail('Exception for unexisting type must be thrown');
        } catch (Exception $e) {
            $this->addToAssertionCount(1);
        }
    }

    /**
     * Test test_validate_field_existance method
     */
    public function test_validate_field_existance()
    {
        // setup and test body
        $FieldsAlgorithms = new \Mezon\GUI\FieldsAlgorithms($this->get_fields_1(), ENTITY_NAME);

        // test body and assertions
        try {
            $FieldsAlgorithms->validate_field_existance('unexisting-field');
            $this->fail('Unexisting field must cause exception');
        } catch (Exception $e) {
            $this->addToAssertionCount(1);
        }

        try {
            $FieldsAlgorithms->validate_field_existance('id');
        } catch (Exception $e) {
            $this->fail('Exception must not be thrown');
        }
    }

    /**
     * Test get_secure_value method
     */
    public function test_get_secure_value()
    {
        // setup and test body
        $FieldsAlgorithms = new \Mezon\GUI\FieldsAlgorithms($this->get_fields_1(), ENTITY_NAME);

        // test body and assertions
        $id = $FieldsAlgorithms->get_secure_value('id', '1');

        // assertions
        $this->assertIsInt($id, 'Invalid secure processing for integer value');
        $this->assertEquals(1, $id, 'Data loss for integer value');
    }

    /**
     * Test get_secure_values method
     */
    public function test_get_secure_values()
    {
        // setup and test body
        $FieldsAlgorithms = new \Mezon\GUI\FieldsAlgorithms($this->get_fields_1(), ENTITY_NAME);

        // test body and assertions
        $id = $FieldsAlgorithms->get_secure_values('id', [
            '1',
            '2&'
        ]);

        // assertions
        $this->assertIsInt($id[0], 'Invalid secure processing for integer values');
        $this->assertIsInt($id[1], 'Invalid secure processing for integer values');

        $this->assertEquals(1, $id[0], 'Data loss for integer values');
        $this->assertEquals(2, $id[1], 'Data loss for integer values');
    }

    /**
     * Test get_values_for_prefix method
     */
    public function test_get_values_for_prefix()
    {
        // setup and test body
        $FieldsAlgorithms = new \Mezon\GUI\FieldsAlgorithms($this->get_fields_1(), ENTITY_NAME);
        $_POST['prefix-id'] = '1';
        $_POST['prefix-title'] = 'some string';

        // test body and assertions
        $Result = $FieldsAlgorithms->get_values_for_prefix('prefix-');

        // assertions
        $this->assertIsInt($Result['id'], 'Invalid secure processing for integer prefix');
        $this->assertIsString($Result[TITLE_FIELD_NAME], 'Invalid secure processing for string prefix');

        $this->assertEquals(1, $Result['id'], 'Data loss for integer preix');
        $this->assertEquals('some string', $Result[TITLE_FIELD_NAME], 'Data loss for string preix');
    }

    /**
     * Testing 'remove_field' method
     */
    public function test_remove_field()
    {
        // setup
        $FieldsAlgorithms = new \Mezon\GUI\FieldsAlgorithms($this->get_fields_1(), ENTITY_NAME);

        // test body
        $FieldsAlgorithms->remove_field('extensions');

        // assertions
        $this->assertFalse($FieldsAlgorithms->has_custom_fields(), 'Field "extensions" was not removed');
    }

    /**
     * Testing 'fetch_custom_field' method for unexisting field
     */
    public function test_fetch_custom_field_unexisting_field()
    {
        // setup
        $FieldsAlgorithms = new \Mezon\GUI\FieldsAlgorithms($this->get_fields_1(), ENTITY_NAME);
        $Record = [];

        // test body
        $Result = $FieldsAlgorithms->fetch_custom_field($Record, 'unexisting');

        // assertions
        $this->assertEquals(0, count($Result), 'Something was returned, but should not');
    }

    /**
     * Testing 'fetch_custom_field' method
     */
    public function test_fetch_custom_field()
    {
        // setup
        $FieldsAlgorithms = new \Mezon\GUI\FieldsAlgorithms($this->get_fields_1(), ENTITY_NAME);
        $Record = [];
        $_POST[ENTITY_NAME . '-balance'] = '11';

        // test body
        $Result = $FieldsAlgorithms->fetch_custom_field($Record, 'extensions');

        // assertions
        $this->assertEquals(11, $Result['balance'], 'Invalid field value');
    }

    /**
     * Testing 'fetch_field' method
     */
    public function test_fetch_field()
    {
        // setup
        $FieldsAlgorithms = new \Mezon\GUI\FieldsAlgorithms($this->get_fields_1(), ENTITY_NAME);
        $Record = [];
        $_POST[ENTITY_NAME . '-id'] = '11';
        $_FILES[ENTITY_NAME . '-avatar'] = [
            'name' => 'test.dat',
            'file' => 'content'
        ];
        $_POST[ENTITY_NAME . '-balance'] = '33';

        // test body
        $FieldsAlgorithms->fetch_field($Record, 'id');
        $FieldsAlgorithms->fetch_field($Record, 'avatar');
        $FieldsAlgorithms->fetch_field($Record, 'extensions');

        // assertions
        $this->assertEquals(11, $Record['id'], 'id was not fetched');
        $Avatar = $Record['avatar'];
        $this->assertFileExists($Avatar, 'File does not exists');
        unlink($Avatar);
        $this->assertEquals(33, $Record['balance'], 'balance was not fetched');
    }

    /**
     * Testing 'get_object' method
     */
    public function test_get_object()
    {
        // setup
        $FieldsAlgorithms = new \Mezon\GUI\FieldsAlgorithms($this->get_fields_1(), ENTITY_NAME);

        // test body
        $Object = $FieldsAlgorithms->get_object('title');

        // assertions
        $this->assertInstanceOf(\Mezon\GUI\Field\InputText::class, $Object);
    }

    /**
     * Testing 'get_fields_names' method
     */
    public function test_get_fields_names()
    {
        // setup
        $FieldsAlgorithms = new \Mezon\GUI\FieldsAlgorithms($this->get_fields_1(), ENTITY_NAME);

        // test body
        $Fields = $FieldsAlgorithms->get_fields_names();

        // assertions
        $this->assertArraySubset([
            'id',
            'title',
            'user_id',
            "label",
            'description',
            'created',
            'avatar',
            'parts',
            'extensions'
        ], $Fields);
    }

    /**
     * Testing field compilation
     */
    public function test_get_compiled_field():void{
        // setup
        $FieldsAlgorithms = new \Mezon\GUI\FieldsAlgorithms($this->get_fields_1(), ENTITY_NAME);

        // test body
        $InputField = $FieldsAlgorithms->get_compiled_field('title');
        $TextareaField = $FieldsAlgorithms->get_compiled_field('description');

        // assertions
        $this->assertStringContainsString('<input ', $InputField);
        $this->assertStringContainsString('<textarea ', $TextareaField);
    }
}

?>