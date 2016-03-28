<?php

    global          $MEZON_PATH;
    require_once( $MEZON_PATH.'/include/php/template-engine.php' );
    require_once( $MEZON_PATH.'/include/php/router.php' );

    /**
    *   Base class of the application.
    */
    class           Application
    {
        /**
        *   Single instance of the class.
        */
        private static          $Instance = false;

        /**
        *   Router object.
        */
        private                 $Router = false;

        /**
        *   Singleton сonstructor.
        */
        function __construct()
        {
            if( self::$Instance === false )
            {
                self::$Instance = $this;
            }

            $this->Router = new Router();
        }

        /**
        *   Processing route.
        */
        public function         run()
        {
            try
            {
                $Route = explode( '/' , trim( @$_GET[ 'r' ] , '/' ) );

                $Content = $this->Router->parse_route( $this , $Route );

                $Engine = new TemplateEngine();
                $Engine->compile_page_vars( $Content );

                print( $Content );
            }
            catch( Exception $e )
            {
                print( '<pre>'.$e );
            }
        }
    }

?>