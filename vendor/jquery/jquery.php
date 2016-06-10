<?php

    require_once( dirname( dirname( dirname( __FILE__ ) ) ).'/conf/conf.php' );
    require_once( $MEZON_PATH.'/vendor/asset/asset.php' );
    require_once( $MEZON_PATH.'/vendor/template-resources/template-resources.php' );

    /**
    *   jQuery asset.
    */
    class jQueryAsset extends Asset
    {
        /**
        *   Constructor.
        *
        *   @param $Mode - pass 'uncompressed' to include minified scripts. All other values will trait as 'min' 
        *   and minified scripts will be used.
        */
        public function __construct( $Mode = 'min' )
        {
            if( $Mode == 'uncompressed' )
            {
                $this->JSFiles [] = 'https://code.jquery.com/jquery-2.2.4.js';
            }
            else
            {
                $this->JSFiles [] = 'https://code.jquery.com/jquery-2.2.4.min.js';
            }
        }
    }

?>