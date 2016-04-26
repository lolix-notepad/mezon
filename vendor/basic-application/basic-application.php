<?php

	require_once( dirname( __FILE__ ).'/../application/application.php' );
	require_once( dirname( __FILE__ ).'/../basic-template/basic-template.php' );

	/**
	*	Basic application wich helps you to test yout ideas and create simple prototypes of your applications.
	*/
	class			BasicApplication extends Application
	{
		/**
		*	Application's template.
		*/
		protected 			$Template = false;

		/**
		*	Constructor.
		*/
		function			__construct()
		{
			parent::__construct();

			$this->Template = new BasicTemplate();
		}

		/**
        *   Running application.
        */
		function			run()
		{
			try
            {
				$this->Template->set_page_var( 'main' , $this->call_route() );

				print( $this->Template );
            }
            catch( Exception $e )
            {
                print( '<pre>'.$e );
            }
		}
	}

?>