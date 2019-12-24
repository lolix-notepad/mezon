<?php
require_once (__DIR__ . '/../label-field.php');

class LabelFieldTest extends PHPUnit\Framework\TestCase
{

    /**
     * Testing constructor
     */
    public function testConstructor()
    {
        // setup
        $Field = new \Mezon\GUI\Field\LabelField([
            'text' => 'name'
        ]);

        // test body
        $Content = $Field->html();

        // assertions
        $this->assertContains('<label class="control-label">name</label>', $Content, 'Label was not generated');
    }

    /**
     * Testing getType method
     */
    public function testGetType(): void
    {
        // setup
        $Field = new \Mezon\GUI\Field\LabelField([
            'text' => 'name'
        ]);

        // test body and assertions
        $this->assertContains('label', $Field->getType());
    }

    /**
     * Testing fillAllRow method
     */
    public function testFillAllRow(): void
    {
        // setup
        $Field = new \Mezon\GUI\Field\LabelField([
            'text' => 'name'
        ]);

        // test body and assertions
        $this->assertTrue($Field->fillAllRow());
    }
}

?>