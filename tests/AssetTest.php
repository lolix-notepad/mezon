<?php

    global          $MEZON_PATH;
    require_once( $MEZON_PATH.'/vendor/asset/asset.php' );

    class       TestingAsset extends Asset
    {
        /**
        *   Setup of the testing data.
        */
        function setup1()
        {
            $this->CSSFiles = array(
                './res/test.css' , './res/test.css' , './res/test.css'
            );

            $this->JSFiles = array(
                './include/js/test.js' , './include/js/test2.js' , './include/js/test.js'
            );
            
            $this->include_files();
        }
    }
    
    class AssetTest extends PHPUnit_Framework_TestCase
    {
        /**
        *   Testing additing CSS file.
        */
        public function testAdditingSingleCSSFile()
        {
            $Asset = new TestingAsset();
            $Asset->setup1();

            $TemplateResources = new TemplateResources();

            $this->assertEquals( 1 , count( $TemplateResources->get_css_files() ) , 'CSS files array must be empty' );
            $this->assertEquals( 2 , count( $TemplateResources->get_js_files() ) , 'CSS files array must be empty' );

            $TemplateResources->clear();
        }
    }

?>