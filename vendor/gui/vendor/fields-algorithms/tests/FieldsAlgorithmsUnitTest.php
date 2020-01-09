<?php
require_once (__DIR__ . '/../../../../../autoloader.php');

define('ID_FIELD_NAME', 'id');
define('TITLE_FIELD_NAME', 'title');
define('USER_ID_FIELD_NAME', 'user_id');
define('FIELDS_FIELD_NAME', 'fields');
define('DISABLED_FIELD_NAME', 'disabled');
define('STRING_TYPE_NAME', 'string');
define('INTEGER_TYPE_NAME', 'integer');
define('DATE_TYPE_NAME', 'date');
define('EXTERNAL_TYPE_NAME', 'external');

class FieldsAlgorithmsUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Post test processing
     */
    public function tearDown(): void
    {
        unset($_GET[FIELDS_FIELD_NAME]);
    }

    /**
     * Method creates testing data
     *
     * @return array Testing data
     */
    protected function getFields1(): array
    {
        return (json_decode(file_get_contents(__DIR__ . '/conf/setup.json'), true));
    }

    /**
     * Method creates testing data
     *
     * @return array Testing data
     */
    protected function getFields2(): array
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
    public function testConstructor()
    {
        // setup and test body
        $FieldsAlgorithms = new \Mezon\Gui\FieldsAlgorithms($this->getFields1(), 'entity');

        // assertions
        $this->assertEquals('entity', $FieldsAlgorithms->getEntityName(), 'EntityName was not set');
        $this->assertTrue($FieldsAlgorithms->hasCustomFields(), 'Data was not loaded');
    }

    /**
     * Testing hasCustomFields
     */
    public function testHasNotCustomFields()
    {
        // setup and test body
        $FieldsAlgorithms = new \Mezon\Gui\FieldsAlgorithms($this->getFields2(), 'entity');

        // assertions
        $_GET[FIELDS_FIELD_NAME] = TITLE_FIELD_NAME;
        $this->assertFalse($FieldsAlgorithms->hasCustomFields(), 'Custom fields are not in the model');
    }

    /**
     * Testing hasCustomFields
     */
    public function testHasCustomFields()
    {
        // setup and test body
        $FieldsAlgorithms = new \Mezon\Gui\FieldsAlgorithms($this->getFields1(), 'entity');

        // assertions
        $this->assertTrue($FieldsAlgorithms->hasCustomFields(), 'Custom fields are in the model');
    }

    /**
     * Testing getTypedValue
     */
    public function testGetTypedValue()
    {
        // setup and test body
        $FieldsAlgorithms = new \Mezon\Gui\FieldsAlgorithms($this->getFields1(), 'entity');

        // assertions int
        $this->assertEquals(
            1,
            $FieldsAlgorithms->getTypedValue(INTEGER_TYPE_NAME, '1'),
            'Type was not casted properly for integer');
        $this->assertTrue(
            is_int($FieldsAlgorithms->getTypedValue(INTEGER_TYPE_NAME, '1')),
            'Type was not casted properly for integer');

        // assertions string
        $this->assertEquals(
            '1',
            $FieldsAlgorithms->getTypedValue(STRING_TYPE_NAME, '1'),
            'Type was not casted properly for string');
        $this->assertTrue(
            is_string($FieldsAlgorithms->getTypedValue(STRING_TYPE_NAME, '1')),
            'Return type is not correct');
        $this->assertEquals(
            '&amp;',
            $FieldsAlgorithms->getTypedValue(STRING_TYPE_NAME, '&'),
            'Type was not casted properly for string');
        $this->assertEquals(
            '',
            $FieldsAlgorithms->getTypedValue(STRING_TYPE_NAME, '""'),
            'Default brunch for string is not working');

        // assertions date
        $this->assertEquals(
            '2019-01-01',
            $FieldsAlgorithms->getTypedValue(DATE_TYPE_NAME, '2019-01-01'),
            'Type was not casted properly for date');
        $this->assertEquals(
            '',
            $FieldsAlgorithms->getTypedValue(DATE_TYPE_NAME, '""'),
            'Default date for string is not working');

        // assertions file
        $this->assertArraySubset([
            'value'
        ], $FieldsAlgorithms->getTypedValue('file', [
            'value'
        ], false), 'Type was returned properly');
        $this->assertFileExists(
            $Path = $FieldsAlgorithms->getTypedValue('file', [
                'name' => 'test.txt',
                'file' => '1234'
            ], true),
            'File was not saved');
        unlink($Path);

        // assertions external
        $this->assertArraySubset([
            1,
            2
        ], $FieldsAlgorithms->getTypedValue(EXTERNAL_TYPE_NAME, [
            '1',
            '2'
        ]), true, 'Type was not casted properly');

        // assertion unexisting
        $this->expectException(Exception::class);
        $FieldsAlgorithms->getTypedValue('unexisting', '1');
    }

    /**
     * Test test_validateFieldExistance method
     */
    public function testValidateFieldExistance()
    {
        // setup and test body
        $FieldsAlgorithms = new \Mezon\Gui\FieldsAlgorithms($this->getFields1(), 'entity');

        // test body and assertions
        $this->expectException(Exception::class);
        $FieldsAlgorithms->validateFieldExistance('unexisting-field');

        $this->expectException(Exception::class);
        $FieldsAlgorithms->validateFieldExistance('id');
    }

    /**
     * Test getSecureValue method
     */
    public function testGetSecureValue()
    {
        // setup and test body
        $FieldsAlgorithms = new \Mezon\Gui\FieldsAlgorithms($this->getFields1(), 'entity');

        // test body and assertions
        $id = $FieldsAlgorithms->getSecureValue('id', '1');

        // assertions
        $this->assertIsInt($id, 'Invalid secure processing for integer value');
        $this->assertEquals(1, $id, 'Data loss for integer value');
    }

    /**
     * Test getSecureValues method
     */
    public function testGetSecureValues()
    {
        // setup and test body
        $FieldsAlgorithms = new \Mezon\Gui\FieldsAlgorithms($this->getFields1(), 'entity');

        // test body and assertions
        $id = $FieldsAlgorithms->getSecureValues('id', [
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
     * Test getValuesForPrefix method
     */
    public function testGetValuesForPrefix()
    {
        // setup and test body
        $FieldsAlgorithms = new \Mezon\Gui\FieldsAlgorithms($this->getFields1(), 'entity');
        $_POST['prefix-id'] = '1';
        $_POST['prefix-title'] = 'some string';

        // test body and assertions
        $Result = $FieldsAlgorithms->getValuesForPrefix('prefix-');

        // assertions
        $this->assertIsInt($Result['id'], 'Invalid secure processing for integer prefix');
        $this->assertIsString($Result[TITLE_FIELD_NAME], 'Invalid secure processing for string prefix');

        $this->assertEquals(1, $Result['id'], 'Data loss for integer preix');
        $this->assertEquals('some string', $Result[TITLE_FIELD_NAME], 'Data loss for string preix');
    }

    /**
     * Testing 'removeField' method
     */
    public function testRemoveField()
    {
        // setup
        $FieldsAlgorithms = new \Mezon\Gui\FieldsAlgorithms($this->getFields1(), 'entity');

        // test body
        $FieldsAlgorithms->removeField('extensions');

        // assertions
        $this->assertFalse($FieldsAlgorithms->hasCustomFields(), 'Field "extensions" was not removed');
    }

    /**
     * Testing 'fetchCustomField' method for unexisting field
     */
    public function testFetchCustomFieldUnexistingField()
    {
        // setup
        $FieldsAlgorithms = new \Mezon\Gui\FieldsAlgorithms($this->getFields1(), 'entity');
        $Record = [];

        // test body
        $Result = $FieldsAlgorithms->fetchCustomField($Record, 'unexisting');

        // assertions
        $this->assertEquals(0, count($Result), 'Something was returned, but should not');
    }

    /**
     * Testing 'fetchCustomField' method
     */
    public function testFetchCustomField()
    {
        // setup
        $FieldsAlgorithms = new \Mezon\Gui\FieldsAlgorithms($this->getFields1(), 'entity');
        $Record = [];
        $_POST['entity' . '-balance'] = '11';

        // test body
        $Result = $FieldsAlgorithms->fetchCustomField($Record, 'extensions');

        // assertions
        $this->assertEquals(11, $Result['balance'], 'Invalid field value');
    }

    /**
     * Testing 'fetchField' method
     */
    public function testFetchField()
    {
        // setup
        $FieldsAlgorithms = new \Mezon\Gui\FieldsAlgorithms($this->getFields1(), 'entity');
        $Record = [];
        $_POST['entity' . '-id'] = '11';
        $_FILES['entity' . '-avatar'] = [
            'name' => 'test.dat',
            'file' => 'content'
        ];
        $_POST['entity' . '-balance'] = '33';

        // test body
        $FieldsAlgorithms->fetchField($Record, 'id');
        $FieldsAlgorithms->fetchField($Record, 'avatar');
        $FieldsAlgorithms->fetchField($Record, 'extensions');

        // assertions
        $this->assertEquals(11, $Record['id'], 'id was not fetched');
        $Avatar = $Record['avatar'];
        $this->assertFileExists($Avatar, 'File does not exists');
        unlink($Avatar);
        $this->assertEquals(33, $Record['balance'], 'balance was not fetched');
    }

    /**
     * Testing 'getObject' method
     */
    public function testGetObject()
    {
        // setup
        $FieldsAlgorithms = new \Mezon\Gui\FieldsAlgorithms($this->getFields1(), 'entity');

        // test body
        $Object = $FieldsAlgorithms->getObject('title');

        // assertions
        $this->assertInstanceOf(\Mezon\Gui\Field\InputText::class, $Object);
    }

    /**
     * Testing 'getFieldsNames' method
     */
    public function testGetFieldsNames()
    {
        // setup
        $FieldsAlgorithms = new \Mezon\Gui\FieldsAlgorithms($this->getFields1(), 'entity');

        // test body
        $Fields = $FieldsAlgorithms->getFieldsNames();

        // assertions
        $this->assertArraySubset(
            [
                'id',
                'title',
                'user_id',
                "label",
                'description',
                'created',
                'avatar',
                'parts',
                'extensions'
            ],
            $Fields);
    }

    /**
     * Testing field compilation
     */
    public function testGetCompiledField(): void
    {
        // setup
        $FieldsAlgorithms = new \Mezon\Gui\FieldsAlgorithms($this->getFields1(), 'entity');

        // test body
        $InputField = $FieldsAlgorithms->getCompiledField('title');
        $TextareaField = $FieldsAlgorithms->getCompiledField('description');

        // assertions
        $this->assertStringContainsString('<input ', $InputField);
        $this->assertStringContainsString('<textarea ', $TextareaField);
    }
}
