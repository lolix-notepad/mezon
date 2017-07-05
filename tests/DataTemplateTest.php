<?php

    global          $MEZON_PATH;

    require_once( $MEZON_PATH.'/vendor/data-template/data-template.php' );
    require_once( $MEZON_PATH.'/vendor/template-resources/template-resources.php' );

    class DataTemplateTest extends PHPUnit\Framework\TestCase
    {
        /**
        *   Generating page.
        */
        public function testTemplateWithoutResources()
        {
            $Template = new DataTemplate();

            $TestString = $Template->compile();

            $this->assertFalse( strpos( $TestString , '{data}' ) !== 0 , 'Variable "data" was not replaced' );
        }
    }

?>