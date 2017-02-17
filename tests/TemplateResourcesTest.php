<?php

    global          $MEZON_PATH;

    require_once( $MEZON_PATH.'/vendor/template-resources/template-resources.php' );

    class TemplateResourcesTest extends PHPUnit\Framework\TestCase
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

        /**
        *   Testing additing CSS files.
        */
        public function testDoublesCSSExcluding()
        {
            $TemplateResources = new TemplateResources();

            $this->assertEquals( 0 , count( $TemplateResources->get_css_files() ) , 'CSS files array must be empty' );

			$TemplateResources->add_css_files( array( './res/test.css' , './res/test.css' ) );

            $this->assertEquals( 1 , count( $TemplateResources->get_css_files() ) , 'Only one path must be added' );

			$TemplateResources->add_css_file( './res/test.css' );

			$this->assertEquals( 1 , count( $TemplateResources->get_css_files() ) , 'Only one path must be added' );

            $TemplateResources->clear();
        }

        /**
        *   Testing additing JS file.
        */
        public function testAdditingSingleJSFile()
        {
            $TemplateResources = new TemplateResources();

            $this->assertEquals( 0 , count( $TemplateResources->get_js_files() ) , 'JS files array must be empty' );

			$TemplateResources->add_js_file( './include/js/test.js' );

			$this->assertEquals( 1 , count( $TemplateResources->get_js_files() ) , 'JS files array must be NOT empty' );

            $TemplateResources->clear();
        }

        /**
        *   Testing additing JS files.
        */
        public function testAdditingMultypleJSFiles()
        {
            $TemplateResources = new TemplateResources();

            $this->assertEquals( 0 , count( $TemplateResources->get_js_files() ) , 'JS files array must be empty' );

			$TemplateResources->add_js_files( array( './include/js/test.js' , './include/js//test2.js' ) );

			$this->assertEquals( 2 , count( $TemplateResources->get_js_files() ) , 'JS files array must be NOT empty' );

            $TemplateResources->clear();
        }

        /**
        *   Testing additing JS files.
        */
        public function testDoublesJSExcluding()
        {
            $TemplateResources = new TemplateResources();

            $this->assertEquals( 0 , count( $TemplateResources->get_js_files() ) , 'JS files array must be empty' );

			$TemplateResources->add_js_files( array( './include/js/test.js' , './include/js/test.js' ) );

            $this->assertEquals( 1 , count( $TemplateResources->get_js_files() ) , 'Only one path must be added' );

			$TemplateResources->add_js_file( './include/js/test.js' );

			$this->assertEquals( 1 , count( $TemplateResources->get_js_files() ) , 'Only one path must be added' );

            $TemplateResources->clear();
        }
    }

?>