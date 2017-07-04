<?php

	/**
	*	User view.
	*/
	class	UsersView extends View
	{
		/**
		*	Type of view.
		*/
		protected $View = 'grid';

		/**
		*	List of users.
		*/
		protected $Users = [];

		/**
        *   Constructor.
        */
        public function		__construct( $View = 'grid' , $Users = array() )
        {
			$this->View = $View;
			$this->Users = $Users;
        }

		/**
		*	Method returns path to the class.
		*/
		protected function	get_path()
		{
			return( dirname( __FILE__ ) );
		}

		/**
		*	Method renders grid of users.
		*/
		protected function	render_grid( $Params )
		{
			$Content  = file_get_contents( 
				$this->get_path().'/res/templates/users-grid-header.tpl'
			);

			foreach( $this->Users as $i => $User )
			{
				$Content .= TemplateEngine::print_record( 
					file_get_contents( $this->get_path().'/res/templates/users-grid-item.tpl' ) ,
					$User
				);
			}

			$Content .= file_get_contents( 
				$this->get_path().'/res/templates/users-grid-footer.tpl'
			);

			return( $Content );
		}

		/**
		*	Method renders create form.
		*/
		protected function	render_create_form( $Params )
		{
			$FormContent = file_get_contents( 
				$this->get_path().'/res/templates/add-user-form.tpl'
			);

			return( array_merge( array( 'main' => $FormContent ) , $Params ) );
		}

		/**
		*	Method renders edit form.
		*/
		protected function	render_edit_form( $Params )
		{
			$FormContent = file_get_contents( 
				$this->get_path().'/res/templates/edit-user-form.tpl'
			);

			return( array_merge( array( 'main' => $FormContent ) , $Params ) );
		}

		/**
		*	Render function.
		*/
		public function		render( $Params )
		{
			switch( $this->View )
			{
				case( 'grid' ):
					return( $this->render_grid( $Params ) );
				break;
				case( 'create_form' ):
					return( $this->render_create_form( $Params ) );
				break;
				case( 'edit_form' ):
					return( $this->render_edit_form( $Params ) );
				break;
				default:
					throw( new Exception( 'Invalid render "'.$this->View.'"' ) );
				break;
			}
		}
	}

?>