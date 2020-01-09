<?php
require_once (__DIR__ . '/../../../../../autoloader.php');

define('SESSION_ID', 'session-id');
define('ENTITY_NAME', 'test-record');

class FormBuilderUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Method returns testing data
     *
     * @param string $Name
     *            File name
     * @return array Testing data
     */
    protected function getJson(string $Name): array
    {
        return (json_decode(file_get_contents(__DIR__ . '/conf/' . $Name . '.json'), true));
    }

    /**
     * Method constructs FieldsAlgorithms object
     *
     * @return \Mezon\Gui\FieldsAlgorithms Fields algorithms object
     */
    protected function getFieldsAlgorithms()
    {
        return (new \Mezon\Gui\FieldsAlgorithms($this->getJson('setup'), 'entity'));
    }

    /**
     * Setting on and off the form title flag.
     *
     * @param bool $Flag
     */
    protected function formHeader(bool $Flag)
    {
        if (! $Flag) {
            $_GET['no-header'] = 1;
        } else {
            unset($_GET['no-header']);
        }
    }

    /**
     * Method returns mock for FormBuilder
     *
     * @return object Mock of the object
     */
    protected function getFormBuilder(bool $HasLayout = true): object
    {
        $FormBuilder = $this->getMockBuilder(\Mezon\Gui\FormBuilder::class)
            ->setMethods([
            'get_external_records'
        ])
            ->setConstructorArgs(
            [
                $this->getFieldsAlgorithms(),
                SESSION_ID,
                ENTITY_NAME,
                $HasLayout ? $this->getJson('layout') : []
            ])
            ->getMock();

        $FormBuilder->method('get_external_records')->willReturn([
            [
                'id' => 1,
                'title' => "Some title"
            ]
        ]);

        return ($FormBuilder);
    }

    /**
     * Testing creation form
     */
    public function testCreationForm(): void
    {
        // setup
        $FormBuilder = $this->getFormBuilder();

        $this->formHeader(true);

        // test body
        $Content = $FormBuilder->creationForm();

        // assertions
        $this->assertContains('<div class="page-title">', $Content, 'No form title was found');
        $this->assertContains('<form', $Content, 'No form tag was found');
        $this->assertContains('<textarea', $Content, 'No textarea tag was found');
        $this->assertContains('<input', $Content, 'No input tag was found');
        $this->assertContains('<select', $Content, 'No select tag was found');
        $this->assertContains('<option', $Content, 'No option tag was found');
        $this->assertContains('type="file"', $Content, 'No file field was found');
    }

    /**
     * Testing creation form
     */
    public function testUpdatingForm(): void
    {
        // setup
        $FormBuilder = $this->getFormBuilder();

        $this->formHeader(true);

        // test body
        $Content = $FormBuilder->updatingForm('session-id', [
            'id' => '23'
        ]);

        // assertions
        $this->assertContains('<div class="page-title">', $Content, 'No form title was found');
        $this->assertContains('<form', $Content, 'No form tag was found');
        $this->assertContains('<textarea', $Content, 'No textarea tag was found');
        $this->assertContains('<input', $Content, 'No input tag was found');
        $this->assertContains('<select', $Content, 'No select tag was found');
        $this->assertContains('<option', $Content, 'No option tag was found');
        $this->assertContains('type="file"', $Content, 'No file field was found');
    }

    /**
     * Testing constructor with no form title
     */
    public function testConstructorNoFormTitle(): void
    {
        // setup
        $_GET['form-width'] = 7;
        $FormBuilder = $this->getFormBuilder(false);

        $this->formHeader(false);

        // test body
        $Content = $FormBuilder->creationForm();

        // assertions
        $this->assertNotContains('<div class="page-title"', $Content, 'Form title was found');
    }
}
