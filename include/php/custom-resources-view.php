<?php

    //TODO: move to vendor
    /**
    *   Class adds custom resources into the 'head' tag.
    */
    class           CustomResourcesView
    {
        /**
        *   Single instance of the class.
        */
        private static       $Instance = false;

        /**
        *   Custom CSS files to be included.
        */
        private static       $CSSFiles = false;

        /**
        *   Construct singleton.
        */
        function __construct()
        {
            if( self::$Instance === false )
            {
                self::$Instance = $this;
            }

            if( self::$CSSFiles === false )
            {
                self::$CSSFiles = array();
            }
        }

        /**
        *   Loading resources.
        */
        function            run()
        {
            $Content = '';

            foreach( self::$CSSFiles as $i => $CSSFile )
            {
                $Content .= '
        <link rel="stylesheet" href="'.$CSSFile.'">';
            }

            return( $Content );
        }

        /**
        *   Additing css file.
        */
        function            add_css_file( $CSSFile )
        {
            self::$CSSFiles [] = expand_string( $CSSFile );
        }
    }

?>