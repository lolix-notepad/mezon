<?php

    /**
    *   Caching subsystem.
    */
    class           CallCache
    {
        /**
        *   Method creates data key.
        */
        private static function data_key( $Param1 , $Param2 , $Param3 , $Param4 , $Param5 )
        {
            return(
                serialize( $Param1 ).serialize( $Param2 ).serialize( $Param3 ).serialize( $Param4 ).serialize( $Param5 )
            );
        }

        /**
        *   Method gets cached values for exact method.
        */
        public static function  get( $Key , $Param1 = '' , $Param2 = '' , $Param3 = '' , $Param4 = '' , $Param5 = '' )
        {
            $DataKey = self::data_key( $Param1 , $Param2 , $Param3 , $Param4 , $Param5 );

            $FileName = $Key.'_'.md5( $DataKey );

            $FilePath = dirname( __FILE__ ).'/data/'.$FileName;

            if( file_exists( $FilePath ) )
            {
                return( unserialize( file_get_contents( $FilePath ) ) );
            }

            return( false );
        }

        /**
        *   Method puts data in cache.
        */
        public static function  put( $Key , $Data , $Param1 = '' , $Param2 = '' , $Param3 = '' , $Param4 = '' , $Param5 = '' )
        {
            $DataKey = self::data_key( $Param1 , $Param2 , $Param3 , $Param4 , $Param5 );

            $FileName = $Key.'_'.md5( $DataKey );

            $FilePath = dirname( __FILE__ ).'/data/'.$FileName;

            file_put_contents( $FilePath , serialize( $Data ) );
        }
    }

?>