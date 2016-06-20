<?php

    require_once( dirname( dirname( dirname( __FILE__ ) ) ).'/conf/conf.php' );
    require_once( $MEZON_PATH.'/vendor/router/router.php' );
    require_once( $MEZON_PATH.'/vendor/template-engine/template-engine.php' );

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
        *   Constructor.
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
        *   Method loads routes from config file.
        */
        public function     load_routes_from_config( $Path = './conf/routes.php' )
        {
            $Routes = ( include( $Path ) );

            foreach( $Routes as $i => $Route )
            {
                if( isset( $Route[ 'route' ] ) == false )
                {
                    throw( new Exception( 'Field "route" must be set' ) );
                }
                if( isset( $Route[ 'callback' ] ) == false )
                {
                    throw( new Exception( 'Field "callback" must be set' ) );
                }
                $this->Router->add_route( 
                    $Route[ 'route' ] , 
                    array( $this , $Route[ 'callback' ] ) , 
                    isset( $Route[ 'method' ] ) ? $Route[ 'method' ] : 'GET'
                );
            }
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