<?php

    require_once( dirname( __FILE__ ).'/../vendor/template-resources/template-resources.php' );

    class TemplateResourcesTest extends PHPUnit_Framework_TestCase
    {
        /**
        *   Testing additing CSS file.
        */
        public function testAdditingSingleCSSFile()
        {
            $TemplateResources = new TemplateResources();

            $this->assertEquals( 0 , count( $TemplateResources->get_css_files() ) , 'CSS files array must be empty' );

			$TemplateResources->add_css_file( './res/test.css' );

			$this->assertEquals( 1 , count( $TemplateResources->get_css_files() ) , 'CSS files array must be NOT empty' );

            $TemplateResources->clear();
        }

        /**
        *   Testing additing CSS files.
        */
        public function testAdditingMultypleCSSFiles()
        {
            $TemplateResources = new TemplateResources();

            $this->assertEquals( 0 , count( $TemplateResources->get_css_files() ) , 'CSS files array must be empty' );

			$TemplateResources->add_css_files( array( './res/test.css' , './res/test2.css' ) );

			$this->assertEquals( 2 , count( $TemplateResources->get_css_files() ) , 'CSS files array must be NOT empty' );

            $TemplateResources->clear();
        }
    }

?>