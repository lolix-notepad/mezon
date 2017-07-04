<?php

	/**
	*	User model.
	*/
	class	Users
	{
		/**
		*	DB fields.
		*/
		protected $Fields = [ 'id' , 'login' , 'password' , 'role' ];

		/**
		*	Connection to DB.
		*/
		protected $CRUD = false;

		/**
        *   Constructor.
        */
        public function		__construct( $CRUD )
        {
			$this->CRUD = $CRUD;
        }

		/**
		*	Method returns all users in the system.
		*
		*	It can be usefull while we don't have many users.
		*/
		public function		get_all()
		{
			// getting actions with active gifts
			$Records = $this->CRUD->select( 
				implode( ', ' , $this->Fields ) , 'mezon_users'
			);

			return( $Records );
		}

		/**
		*	Method returns user by it's login.
		*/
		public function		get_by_login( $Login )
		{
			$Records = $this->CRUD->select( 
				implode( ', ' , $this->Fields ) , 'mezon_users' , 'login LIKE "'.htmlspecialchars( $Login ).'"'
			);

			if( count( $Records ) )
			{
				return( $Records[ 0 ] );
			}

			throw( new Exception( 'Record with login "'.$Login.'" was not found' ) );
		}

		/**
		*	Method returns record by it's id.
		*/
		public function		get_by_id( $id )
		{
			$Records = $this->CRUD->select( 
				implode( ', ' , $this->Fields ) , 'mezon_users' , 'id = '.intval( $id )
			);

			if( count( $Records ) )
			{
				return( $Records[ 0 ] );
			}

			throw( new Exception( 'Record with id "'.$id.'" was not found' ) );
		}

		/**
		*	Method creates record.
		*/
		public function		insert( $Record )
		{
			return( $this->CRUD->insert( 'mezon_users' , $Record ) );
		}

		/**
		*	Method updates record.
		*/
		public function		update( $id , $Record )
		{
			return( $this->CRUD->update( 'mezon_users' , $Record , 'id = '.$id ) );
		}

		/**
		*	Method deletes record.
		*/
		public function		delete( $id )
		{
			return( $this->CRUD->delete( 'mezon_users' , 'id = '.$id ) );
		}
	}

?>