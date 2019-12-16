<?php
require_once (__DIR__ . '/../../fields-algorithms/fields-algorithms.php');
require_once (__DIR__ . '/../../../../functional/functional.php');

require_once (__DIR__ . '/../form-builder.php');

define('SESSION_ID', 'session-id');
define('ENTITY_NAME', 'test-record');

class FormBuilderUnitTest extends PHPUnit\Framework\TestCase
{

    /**
     * Method returns testing data
     *
     * @param string $Name
     *            File name
     * @return array Testing data
     */
    protected function get_json(string $Name): array
    {
        return (json_decode(file_get_contents(__DIR__ . '/conf/' . $Name . '.json'), true));
    }

    /**
     * Method constructs FieldsAlgorithms object
     *
     * @return FieldsAlgorithms Fields algorithms object
     */
    protected function get_fields_algorithms()
    {
        return (new FieldsAlgorithms($this->get_json('setup'), 'entity'));
    }

    /**
     * Setting on and off the form title flag.
     *
     * @param bool $Flag
     */
    protected function form_header(bool $Flag)
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
    protected function get_form_builder(bool $HasLayout = true): object
    {
        $FormBuilder = $this->getMockBuilder('FormBuilder')
            ->setMethods([
            'get_external_records'
        ])
            ->setConstructorArgs([
            $this->get_fields_algorithms(),
            SESSION_ID,
            ENTITY_NAME,
            $HasLayout ? $this->get_json('layout') : []
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
    public function test_creation_form(): void
    {
        // setup
        $FormBuilder = $this->get_form_builder();

        $this->form_header(true);

        // test body
        $Content = $FormBuilder->creation_form();

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
    public function test_updating_form(): void
    {
        // setup
        $FormBuilder = $this->get_form_builder();

        $this->form_header(true);

        // test body
        $Content = $FormBuilder->updating_form('session-id', [
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
    public function test_constructor_no_form_title(): void
    {
        // setup
        $_GET['form-width'] = 7;
        $FormBuilder = $this->get_form_builder(false);

        $this->form_header(false);

        // test body
        $Content = $FormBuilder->creation_form();

        // assertions
        $this->assertNotContains('<div class="page-title"', $Content, 'Form title was found');
    }
}

?>