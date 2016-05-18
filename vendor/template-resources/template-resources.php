<?php

    global          $MEZON_PATH;
    require_once( $MEZON_PATH.'/conf/conf.php' );

    /**
    *   Class collects resources for page.
    *
    *   Any including components can add to the page their own resources without having access to the application or template.
    */
    class         TemplateResources
    {
        /**
        *   Custom CSS files to be included.
        */
        private static     $CSSFiles = false;

        /**
        *   Constructor.
        */
        function __construct()
        {
            if( self::$CSSFiles === false )
            {
                self::$CSSFiles = array();
            }
        }

        /**
        *   Additing single CSS file.
        */
        function        add_css_file( $CSSFile )
        {
            self::$CSSFiles [] = _expand_string( $CSSFile );
        }

        /**
        *   Additing multyple CSS files.
        */
        function        add_css_files( $CSSFiles )
        {
            self::$CSSFiles = array_merge( self::$CSSFiles , _expand_string( $CSSFiles ) );
        }

        /**
        *   Method returning added CSS files.
        */
        function        get_css_files()
        {
            return( self::$CSSFiles );
        }

        /**
        *   Method clears loaded resources.
        */
        function        clear()
        {
            self::$CSSFiles = array();
        }
    }

    // creating global object
    new TemplateResources();

?>