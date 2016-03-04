<?php

    /**
    *   Class adds custom resources into the 'head' tag.
    */
    class           CustomResourcesView
    {
        public static       $Instance = false;

        public static       $CSSFiles = false;

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
        static function     add_css_file( $CSSFile )
        {
            self::$CSSFiles [] = expand_string( $CSSFile );
        }
    }

?>