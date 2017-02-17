<?php

    require_once( dirname( dirname( dirname( __FILE__ ) ) ).'/conf/conf.php' );
    require_once( MEZON_PATH.'/vendor/singleton/singleton.php' );

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

        /**
        *   Method returns block's start and end.
        */
        protected static function       get_block_positions( $String , $BlockStart , $BlockEnd )
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

        /**
        *   Method returns content between {$BlockStart} and {$BlockEnd} tags.
        */
        public static function      get_block_data( $Str , $BlockStart , $BlockEnd )
		{
            list( $StartPos , $EndPos ) = self::get_block_positions( 
                $Str , $BlockStart , $BlockEnd
            );

            if( $StartPos !== false )
            {
                $BlockData = substr( 
                    $Str , 
                    $StartPos + strlen( '{'.$BlockStart.'}' ) , 
                    $EndPos - $StartPos - strlen( '{'.$BlockStart.'}' )
                );

                return( $BlockData );
            }
            else
            {
                throw( new Exception( 'An error occured while getting block data' ) );
            }
		}

        /**
        *   Getting macro start.
		*/
        protected static function   handle_macro_start( $TmpStartPos , $TmpEndPos , &$StartPos , &$Counter )
		{
            if( $TmpStartPos !== false && $TmpEndPos !== false )
            {
                if( $TmpStartPos < $TmpEndPos )
                {
                    $StartPos = $TmpEndPos;
                }
                if( $TmpEndPos < $TmpStartPos )
                {
                    $Counter--;
                    if( $Counter )
                    {
                        $Counter++;
                    }
                    $StartPos = $TmpStartPos;
                }
            }
		}

		/**
        *   Getting macro end.
		*/
		protected static function   handle_macro_end( $TmpStartPos , $TmpEndPos , &$StartPos , 
                                                                        &$Counter , $MacroStartPos )
		{
            if( $TmpStartPos !== false && $TmpEndPos === false )
            {
                $Counter++;
                $StartPos = $TmpStartPos;
            }

            if( $TmpStartPos === false && $TmpEndPos !== false )
            {
                $Counter--;
                $StartPos = $TmpEndPos;
            }

            if( $TmpStartPos === false && $TmpEndPos === false )
            {
                /* ничего не найдено, поэтому внешний цикл закончен, да и внутренний тоже
                   $StartPos = strlen( $StringData ); */
                $StartPos = $MacroStartPos;
            }
		}
        
        /**
        *   Getting macro bounds.
        */
        protected static function   handle_macro_start_end( &$StringData , &$TmpStartPos , &$TmpEndPos , 
																		&$StartPos , &$Counter , $MacroStartPos )
		{
            $TmpStartPos = strpos( $StringData , '{' , $StartPos + 1 );
            $TmpEndPos = strpos( $StringData , '}' , $StartPos + 1 );

            self::handle_macro_start( $TmpStartPos , $TmpEndPos , $StartPos , $Counter );

            self::handle_macro_end( 
                $TmpStartPos , $TmpEndPos , $StartPos , $Counter , $MacroStartPos
            );
		}

        /**
        *   Getting macro start.
        */
        public static function      find_macro( &$StringData , &$TmpStartPos , &$TmpEndPos , 
										&$StartPos , &$Counter , $MacroStartPos , $ParamStartPos )
		{
            do
            {
                self::handle_macro_start_end( 
                    $StringData , $TmpStartPos , $TmpEndPos , $StartPos , $Counter , $MacroStartPos
                );

                if( $Counter == 0 )
                {
                    return( substr( $StringData , $ParamStartPos , $TmpEndPos - $ParamStartPos ) );
                }
            }
            while( $TmpStartPos );

            return( false );
		}

        /**
        *   Method fetches macro parameters.
        */
        public static function      get_macro_parameters( $Str , $Name , $StartPos = -1 )
		{
            for( ; ( $TmpStartPos = strpos( $Str , '{'.$Name.':' , $StartPos + 1 ) ) !== false ; )
            {
                $Counter = 1;
                $StartPos = $TmpEndPos = $TmpStartPos;

                $MacroStartPos = $StartPos;
                $ParamStartPos = $MacroStartPos + strlen( '{'.$Name.':' );

                $Result = self::find_macro( $Str , $TmpStartPos , $TmpEndPos , 
                                    $StartPos , $Counter , $MacroStartPos , $ParamStartPos );

                if( $Result !== false )
                {
                    return( $Result );
                }
            }

            return( false );
		}

        /**
        *   Method applyes data for foreach block content.
        */
        protected static function	apply_foreach_data( $Str , $Parameters , $Data )
		{
            $SubTemplate = self::get_block_data( $Str , "foreach:$Parameters" , '~foreach' );
            $BlockStart = "{foreach:$Parameters}";

            foreach( $Data as $k => $v )
            {
                $Str = str_replace( $BlockStart , self::print_record( $SubTemplate , $v ).$BlockStart , $Str );
            }

            return( $Str );
		}

        /**
        *   Method replaces block with content.
        */
        public static function      replace_block( $Str , $BlockStart , $BlockEnd , $Content )
		{
            list( $StartPos , $EndPos ) = self::get_block_positions( 
                $Str , $BlockStart , $BlockEnd
            );

            if( $StartPos !== false )
            {
                $Str = substr_replace( $Str , 
                    $Content , 
                    $StartPos , 
                    $EndPos - $StartPos + strlen( chr( 123 ).$BlockEnd.chr( 125 ) ) 
                );
            }

            return( $Str );
		}

        /**
        *   Method processes 'foreach' macro.
        */
        public static function      compile_foreach( $Str , &$Record )
		{
            $StartPos = -1;

            for( ; $Parameters = self::get_macro_parameters( $Str , 'foreach' , $StartPos ) ; )
            {
                if( isset( $Record->$Parameters ) !== false || isset( $Record[ $Parameters ] ) !== false )
                {
                    if( is_array( $Record ) )$Data = $Record[ $Parameters ];
                    if( is_object( $Record ) )$Data = $Record->$Parameters;

                    $Str = self::apply_foreach_data( $Str , $Parameters , $Data );

                    $Str = self::replace_block( $Str , "foreach:$Parameters" , '~foreach' , '' );
                }
                else
                {
                    $StartPos = strpos( $Str , "{foreach:$Parameters" , $StartPos > 0 ? $StartPos : 0 );
                }
            }

            return( $Str );
		}

        /**
        *   Method replaces all {var-name} placeholders in $String with fields from $Record.
        */
        public static function      print_record( $String , $Record )
        {
            if( is_array( $Record ) === false && is_object( $Record ) === false )
            {
                throw( new Exception( 'Invalid record was passed' ) );
            }

            $String = self::compile_foreach( $String , $Record );

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