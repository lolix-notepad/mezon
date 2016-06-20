<?php

	require_once( dirname( __FILE__ ).'/../application/application.php' );
	require_once( dirname( __FILE__ ).'/../basic-template/basic-template.php' );

	/**
	*	Basic application wich helps you to test yout ideas and create simple prototypes of your applications.
    *
    *   It is simply Application + BasicTemplate wich allows to construct applications with unified templates. And for all pages template will be the same.
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
                $Result = $this->call_route();

                if( is_array( $Result ) )
                {
                    foreach( $Result as $Key => $Value )
                    {
                        $this->Template->set_page_var( $Key , $Value );
                    }
                }
                else
                {
                    $this->Template->set_page_var( 'main' , $Result );
                }

				print( $this->Template->compile() );
            }
            catch( Exception $e )
            {
                print( '<pre>'.$e );
            }
		}
	}

?>