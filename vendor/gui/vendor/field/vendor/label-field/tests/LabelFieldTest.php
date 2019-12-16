<?php
require_once (__DIR__ . '/../label-field.php');

class LabelFieldTest extends PHPUnit\Framework\TestCase
{

    /**
     * Testing constructor
     */
    public function test_constructor()
    {
        // setup
        $Field = new LabelField([
            'text' => 'name'
        ]);

        // test body
        $Content = $Field->html();

        // assertions
        $this->assertContains('<label class="control-label">name</label>', $Content, 'Label was not generated');
    }
}

?>