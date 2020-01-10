<?php
require_once (__DIR__ . '/../../../../../../../autoloader.php');

class LabelFieldUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing constructor
     */
    public function testConstructor()
    {
        // setup
        $Field = new \Mezon\Gui\Field\LabelField([
            'text' => 'name'
        ]);

        // test body
        $Content = $Field->html();

        // assertions
        $this->assertStringContainsString('<label class="control-label">name</label>', $Content, 'Label was not generated');
    }

    /**
     * Testing getType method
     */
    public function testGetType(): void
    {
        // setup
        $Field = new \Mezon\Gui\Field\LabelField([
            'text' => 'name'
        ]);

        // test body and assertions
        $this->assertStringContainsString('label', $Field->getType());
    }

    /**
     * Testing fillAllRow method
     */
    public function testFillAllRow(): void
    {
        // setup
        $Field = new \Mezon\Gui\Field\LabelField([
            'text' => 'name'
        ]);

        // test body and assertions
        $this->assertTrue($Field->fillAllRow());
    }
}
