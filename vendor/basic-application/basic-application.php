<?php

	require_once( dirname( __FILE__ ).'/../basic-template/basic-template.php' );
	require_once( dirname( __FILE__ ).'/../common-application/common-application.php' );
	require_once( dirname( __FILE__ ).'/../view/view.php' );

	/**
	*	Basic application wich helps you to test yout ideas and create simple prototypes of your applications.
    *
    *   It is simply Application + BasicTemplate wich allows to construct applications with unified templates. And for all pages template will be the same.
	*/
	class			BasicApplication extends CommonApplication
	{
		/**
		*	Constructor.
		*/
		function			__construct()
		{
			parent::__construct( new BasicTemplate() );
		}
	}

?>