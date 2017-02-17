<?php

    require_once( dirname( dirname( dirname( __FILE__ ) ) ).'/conf/conf.php' );
    require_once( MEZON_PATH.'/conf/conf.php' );

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
        *   Custom JS files to be included.
        */
        private static     $JSFiles = false;

        /**
        *   Constructor.
        */
        function __construct()
        {
            if( self::$CSSFiles === false )
            {
                self::$CSSFiles = array();
            }
            if( self::$JSFiles === false )
            {
                self::$JSFiles = array();
            }
        }

        /**
        *   Additing single CSS file.
        */
        function        add_css_file( $CSSFile )
        {
            // additing only unique paths
            if( array_search( $CSSFile , self::$CSSFiles ) === false )
            {
                self::$CSSFiles [] = _expand_string( $CSSFile );
            }
        }

        /**
        *   Additing multyple CSS files.
        */
        function        add_css_files( $CSSFiles )
        {
            foreach( $CSSFiles as $i => $CSSFile )
            {
                $this->add_css_file( $CSSFile );
            }
        }

        /**
        *   Method returning added CSS files.
        */
        function        get_css_files()
        {
            return( self::$CSSFiles );
        }

        /**
        *   Additing single CSS file.
        */
        function        add_js_file( $JSFile )
        {
            // additing only unique paths
            if( array_search( $JSFile , self::$JSFiles ) === false )
            {
                self::$JSFiles [] = _expand_string( $JSFile );
            }
        }

        /**
        *   Additing multyple CSS files.
        */
        function        add_js_files( $JSFiles )
        {
            foreach( $JSFiles as $i => $JSFile )
            {
                $this->add_js_file( $JSFile );
            }
        }
        
        /**
        *   Method returning added JS files.
        */
        function        get_js_files()
        {
            return( self::$JSFiles );
        }

        /**
        *   Method clears loaded resources.
        */
        function        clear()
        {
            self::$CSSFiles = array();

            self::$JSFiles = array();
        }
    }

    // creating global object
    new TemplateResources();

?>