<?php

    require_once( dirname( dirname( dirname( __FILE__ ) ) ).'/conf/conf.php' );
    require_once( $MEZON_PATH.'/vendor/singleton/singleton.php' );

	/**
	*	Template engine class.
	*/
    class           TemplateEngine extends Singleton
    {
        /**
        *   Page variables.
        */
        private $PageVars = array();

        /**
        *   Setting page variables.
        */
        public function         set_page_var( $Var , $Value )
        {
            $this->PageVars[ $Var ] = $Value;
        }

        /**
        *   Compiling the page with it's variables.
        */
        public function         compile_page_vars( &$Content )
        {
            foreach( $this->PageVars as $Key => $Value )
            {
                $Content = str_replace( '{'.$Key.'}' , $Value , $Content );
            }
        }

        /**
        *   Method returns starts and ends of the block.
        */
        protected static function  get_possible_block_positions( &$Positions )
		{
            $StartPos = $EndPos = false;
            $c = 0;

            foreach( $Positions as $Key => $Value )
            {
                if( $StartPos === false && $Value === 's' )
                {
                    $c++;
                    $StartPos = $Key;
                }
                elseif( $EndPos === false && $Value === 'e' && $c === 1 )
                {
                    $EndPos = $Key;
                    break;
                }
                elseif( $Value === 's' || $Value === 'e' && $c > 0 )
                {
                    $c += $Value === 's' ? 1 : -1;
                }
            }

            return( array( $StartPos , $EndPos ) );
		}

        /**
        *   Method returns block's start and end.
        */
        protected static function   get_all_block_positions( $String , $BlockStart , $BlockEnd )
		{
			try
			{
				$Positions = array();
				$StartPos = strpos( $String , '{'.$BlockStart.'}' , 0 );
				$EndPos = -1;

				if( $StartPos !== false )
				{
					$Positions [ $StartPos ] = 's';
					$BlockStart = explode( ':' , $BlockStart );
					$BlockStart = $BlockStart[ 0 ];
					for( ; ( $StartPos = strpos( $String , '{'.$BlockStart.':' , $StartPos + 1 ) ) !== false ; )
					{
						$Positions [ $StartPos ] = 's';
					}
				}
				for( ; $EndPos = strpos( $String , '{'.$BlockEnd.'}' , $EndPos + 1 ) ; )
				{
					$Positions [ $EndPos ] = 'e';
				}
				ksort( $Positions );

				return( $Positions );
			}
			catch( Exception $e )
			{
				$a = func_get_args();_throw_exception_object( __METHOD__ , $a , $e );
			}
		}

        /**
        *   Method returns block's start and end.
        */
        static function             get_block_positions( $String , $BlockStart , $BlockEnd )
		{
			try
			{
				$Positions = self::get_all_block_positions( $String , $BlockStart , $BlockEnd );

				list( $StartPos , $EndPos ) = self::get_possible_block_positions( $Positions );

				if( $StartPos === false )
				{
					return( array( false , false ) );
				}
				if( $EndPos === false )
				{
					throw( new Exception( 'Block end was not found' ) );
				}

				return( array( $StartPos , $EndPos ) );
			}
			catch( Exception $e )
			{
				$a = func_get_args();_throw_exception_object( __METHOD__ , $a , $e );
			}
		}

        /**
        *   Method replaces all {var-name} placeholders in $String with fields from $Record.
        */
        public static function  print_record( $String , $Record )
        {
            if( is_array( $Record ) === false && is_object( $Record ) === false )
            {
                throw( new Exception( 'Invalid record was passed' ) );
            }

            foreach( $Record as $Field => $Value )
            {
                if( is_array( $Value ) || is_object( $Value ) )
                {
                    $String = TemplateEngine::print_record( $String , $Value );
                }
                else
                {
                    $String = str_replace( '{'.$Field.'}' , $Value , $String );
                }
            }

            return( $String );
        }
    }

?>