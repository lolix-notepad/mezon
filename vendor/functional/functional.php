<?php

    /**
    *   Wrapper for Yandex Metrika REST API
    */
    class           Functional
    {
        /**
        *   Method fetches all fields from objects/arrays of an array.
        */
        public static function     get_fields( $Data , $Field )
        {
            $Return = array();

            foreach( $Data as $i => $Record )
            {
				if( is_object( $Record ) )
				{
					$Return [] = $Record->$Field;
				}
				else
				{
					$Return [] = $Record[ $Field ];
				}
            }

            return( $Return );
        }

        /**
        *   Method sets fields $FieldName in array of objects $Objects with $Values.
        */
        public static function      set_fields_in_objects( &$Objects , $FieldName , $Values )
        {
            foreach( $Values as $i => $Value )
            {
                if( isset( $Objects[ $i ] ) === false )
                {
                    $Objects[ $i ] = new stdClass();
                }

                $Objects[ $i ]->$FieldName = $Value;
            }
        }

        /**
        *   Method sums fields in an array of objects.
        */
        public static function      sum_fields( &$Objects , $FieldName )
        {
            $Sum = 0;

            foreach( $Objects as $i => $Object )
            {
                if( is_array( $Object ) )
                {
                    $Sum += self::sum_fields( $Object , $FieldName );
                }
                else
                {
                    $Sum += $Object->$FieldName;
                }
            }

            return( $Sum );
        }

        /**
        *   Method transforms objects in array.
        */
        public static function      transform( &$Objects , $Transformer )
        {
            foreach( $Objects as $i => $Object )
            {
                $Objects[ $i ] = call_user_func( $Transformer , $Object );
            }
        }

        /**
        *   Method filters objects in array.
        */
        public static function      filter( &$Objects , $Field , $Operation = '==' , $Value = false )
        {
            $Return = array();

            foreach( $Objects as $i => $Object )
            {
                if( is_array( $Object ) )
                {
                    $Return = array_merge( $Return , self::filter( $Object , $Field , $Operation , $Value ) );
                }
                elseif( $Operation == '==' && $Object->$Field == $Value )
                {
                    $Return [] = $Object;
                }
                elseif( $Operation == '>' && $Object->$Field > $Value )
                {
                    $Return [] = $Object;
                }
                elseif( $Operation == '<' && $Object->$Field < $Value )
                {
                    $Return [] = $Object;
                }
            }

            return( $Return );
        }
    }

?>