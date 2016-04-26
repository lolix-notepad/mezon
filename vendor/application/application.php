<?php

    global          $MEZON_PATH;
    require_once( $MEZON_PATH.'/vendor/template-engine/template-engine.php' );
    require_once( $MEZON_PATH.'/include/php/router.php' );

    /**
    *   Base class of the application.
    */
    class           Application
    {
        /**
        *   Router object.
        */
        protected			$Router = false;

        /**
        *   Singleton сonstructor.
        */
        function			__construct()
        {
            // getting application's actions
            $this->Router = new Router();

            $this->Router->fetch_actions( $this );
        }

		/**
		*	Method calls route and returns it's content.
		*/
		protected function	call_route()
		{
			$Route = explode( '/' , trim( @$_GET[ 'r' ] , '/' ) );

            $Content = $this->Router->call_route( $Route );

			return( $Content );
		}

        /**
        *   Running application.
        */
        public function		run()
        {
            try
            {
                print( $this->call_route() );
            }
            catch( Exception $e )
            {
                print( '<pre>'.$e );
            }
        }
    }

?>