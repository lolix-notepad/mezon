<?php

    global          $MEZON_PATH;

    require_once( $MEZON_PATH.'/vendor/basic-template/basic-template.php' );
    require_once( $MEZON_PATH.'/vendor/jquery/jquery.php' );

    class jQueryTest extends PHPUnit_Framework_TestCase
    {
        /**
        *   Including uncompressed JS.
        */
        public function testUncompressedJS()
        {
            $Template = new BasicTemplate();
            $Asset = new jQueryAsset( 'uncompressed' );
            $Asset->include_files();

            $TestString = $Template->compile();

            $this->assertFalse( strpos( $TestString , '="https://code.jquery.com/jquery-2.2.4.js"' ) === false , 'Invalid JS file was included' );
        }

        /**
        *   Including compressed JS.
        */
        public function testCompressedJS1()
        {
            $Template = new BasicTemplate();
            $Asset = new jQueryAsset( 'min' );
            $Asset->include_files();

            $TestString = $Template->compile();

            $this->assertFalse( strpos( $TestString , '="https://code.jquery.com/jquery-2.2.4.min.js"' ) === false , 'Invalid JS file was included' );
        }

        /**
        *   Including compressed JS.
        */
        public function testCompressedJS2()
        {
            $Template = new BasicTemplate();
            $Asset = new jQueryAsset();
            $Asset->include_files();

            $TestString = $Template->compile();

            $this->assertFalse( strpos( $TestString , '="https://code.jquery.com/jquery-2.2.4.min.js"' ) === false , 'Invalid JS file was included' );
        }
    }

?>