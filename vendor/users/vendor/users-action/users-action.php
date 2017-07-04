<?php

	/**
	*	User action.
	*/
	class	UsersAction
	{
		/**
		*	List of roles.
		*/
		var						$Roles = false;

		/**
		*	Constructor.
		*/
		public function			__construct()
		{
			$this->Roles = array( 
				array( 'id' => 'admin' , 'role' => 'Admin user' ) , 
				array( 'id' => 'common' , 'role' => 'Common user' ) , 
			);
		}

		/**
		*	Method returns view.
		*/
		protected function		get_view( $View , $Records = [] )
		{
			return( new UsersView( $View , $Records ) );
		}

		/**
		*	Method returns model.
		*/
		protected function		get_model()
		{
			return( new Users( Config::get_db_connection() ) );
		}

		/**
		*	Grid of system users.
		*/
		public function			grid()
		{
			$Users = $this->get_model();

			return( 
				array( 
					'title' => 'Список пользователей' , 
					'main' => $this->get_view( 'grid' , $Users->get_all() )
				)
			);
		}

		/**
		*	Method displays add user form.
		*/
		public function			add_user()
		{
			$UsersView = $this->get_view( 'create_form' );

			$Return = array( 
				'title' => 'Создание пользователя' , 
				'role-list' => GUI::select_control( 'role' , $this->Roles , 'id' , 'role' )
			);

			if( isset( $_POST[ 'role' ] ) )
			{
				$Record = array(
					'role' => $_POST[ 'role' ] , 
					'login' => $_POST[ 'login' ] , 
					'password' => $_POST[ 'password' ]
				);

				$Users = $this->get_model();
				$Users->insert( $Record );

				$Return[ 'message' ] = GentellaTemplate::success_message_content(
					'Пользователь успешно создан'
				);
			}
			else
			{
				$Return[ 'message' ] = '';
			}

			return( $UsersView->render( $Return ) );
		}

		/**
		*	Method displays edit user form.
		*/
		public function			edit_user( $Route , $Params )
		{
			$UsersView = $this->get_view( 'edit_form' );

			$Users = $this->get_model();
			$User = $Users->get_by_id( $Params[ 'user_id' ] );

			$RoleList = GUI::select_control( 
				'role' , $this->Roles , 'id' , 'role' , $User[ 'role' ]
			);

			$Return = array( 
				'title' => 'Редактирование пользователя' , 
				'role-list' => $RoleList , 
				'login' => $User[ 'login' ] , 
				'password' => $User[ 'password' ]
			);
			
			if( isset( $_POST[ 'role' ] ) )
			{
				$Record = array(
					'role' => $_POST[ 'role' ] , 
					'login' => $_POST[ 'login' ] , 
					'password' => $_POST[ 'password' ]
				);

				$Users->update( $Params[ 'user_id' ] , $Record );

				header( 'Location: ../../' );
				exit( 0 );
			}
			else
			{
				$Return[ 'message' ] = '';
			}

			return( $UsersView->render( $Return ) );
		}

		/**
		*	Method deletes user.
		*/
		public function			delete_user( $Route , $Params )
		{
			$User = $this->get_model();
			$User = $User->delete( $Params[ 'user_id' ] );

			header( 'Location: ../../' );
			exit( 0 );
		}
	}

?>