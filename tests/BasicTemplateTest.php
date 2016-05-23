<?php

    global          $MEZON_PATH;
    $MEZON_PATH = dirname( dirname( __FILE__ ) );

    require_once( $MEZON_PATH.'/vendor/basic-application/basic-application.php' );
    require_once( $MEZON_PATH.'/vendor/basic-template/basic-template.php' );

    class BasicTemplateTest extends PHPUnit_Framework_TestCase
    {
        /**
        *   Generating page without resources.
        */
        public function testTemplateWithoutResources()
        {
            $Template = new BasicTemplate();

            $TestString = $Template->compile();

            $this->assertFalse( strpos( $TestString , '{resources}' ) , 'Variable "resources" was not replaced' );
        }

        /**
        *   Generating page with resources.
        */
        public function testTemplateWithResources()
        {
            $Template = new BasicTemplate();

            $Resources = new TemplateResources();
            $Resources->add_css_file( './res/css/testing-css-file' );

            $TestString = $Template->compile();

            $Resources->clear();

            $this->assertFalse( strpos( $TestString , '="/res/css/testing-css-file"' ) , 'Variable "resources" was not replaced' );
        }
    }

?>