<?php

    global          $MEZON_PATH;

    require_once( $MEZON_PATH.'/vendor/rest-template/rest-template.php' );
    require_once( $MEZON_PATH.'/vendor/template-resources/template-resources.php' );

    class RESTTemplateTest extends PHPUnit\Framework\TestCase
    {
        /**
        *   Generating page.
        */
        public function testTemplateWithoutResources()
        {
            $Template = new RESTTemplate();

            $TestString = $Template->compile();

            $this->assertFalse( strpos( $TestString , '{response}' ) !== 0 , 'Variable "response" was not replaced' );
        }
    }

?>