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