<?php

    /**
    *   Wrapper for Yandex Metrika REST API
    */
    class           Functional
    {
        /**
        *   MEthod fetches all fields from objects of an array.
        */
        public static function     get_fields( $Data , $Field )
        {
            $Return = array();

            foreach( $Data as $i => $Record )
            {
                $Return [] = $Record->$Field;
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
                $Sum += $Object->$FieldName;
            }

            return( $Sum );
        }
    }

?>