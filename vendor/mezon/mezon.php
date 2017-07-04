<?php

	require_once( dirname( __FILE__ ).'/../pdo-crud/pdo-crud.php' );

	/**
	*	Mezon's main class.
	*/
	class	Mezon
	{
		/**
		*	Connection to DB.
		*/
		protected static $CRUD = false;

		/**
		*	Method returns database connection.
		*/
		public static function	get_db_connection( $ConnectionName = 'default-db-connection' )
		{
			if( self::$CRUD !== false )
			{
				return( self::$CRUD );
			}

			self::$CRUD = new PdoCrud();
			self::$CRUD->connect(
				array(
					'dsn' => get_config_value( $ConnectionName.'/dsn' ) , 
					'user' => get_config_value( $ConnectionName.'/user' ) , 
					'password' => get_config_value( $ConnectionName.'/password' )
				)
			);

			return( self::$CRUD );
		}
	}

?>