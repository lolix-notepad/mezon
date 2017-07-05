<?php

    require_once( dirname( dirname( dirname( __FILE__ ) ) ).'/conf/conf.php' );
    require_once( MEZON_PATH.'/vendor/template-resources/template-resources.php' );

    /**
    *   Method compiles select control.
    */
    class           GUI
    {
        /**
		*	Method compiles select control.
		*/
		public static function	select_control( $Name , $Records , $id = 'id' , 
														$Title = 'title' , $SelectedValue = false )
		{
			$Control = '<select class="form-control" name="'.$Name.'">';

			foreach( $Records as $i => $Record )
			{
				$Control .= '<option '.( $SelectedValue == $Record[ $id ] ? 'selected ' : ' ' ).
					'value="'.$Record[ $id ].'">'.$Record[ $Title ].'</option>';
			}

			return( $Control.'</select>' );
		}
    }

?>