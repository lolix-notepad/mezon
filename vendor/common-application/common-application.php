<?php

	require_once( dirname( __FILE__ ).'/../application/application.php' );
	require_once( dirname( __FILE__ ).'/../view/view.php' );

	/**
	*	Common application with any available template.
	*/
	class			CommonApplication extends Application
	{
		/**
		*	Application's template.
		*/
		protected 			$Template = false;

		/**
		*	Constructor.
		*/
		function			__construct( $Template )
		{
			parent::__construct();

			$this->Template = $Template;
		}

		/**
		*	Method renders common parts of all pages.
		*/
		function			cross_render()
		{
			return( array() );
		}

		/**
        *   Running application.
        */
		function			run()
		{
			try
            {
				$Result = $this->cross_render();

                $Result = array_merge( $Result , $this->call_route() );

                if( is_array( $Result ) )
                {
                    foreach( $Result as $Key => $Value )
                    {
                        $Content = $Value instanceof View ? $Value->render( [] ) : $Value;

                        $this->Template->set_page_var( $Key , $Content );
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