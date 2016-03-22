<?php

    //TODO: move to vendor
    class           TemplateView extends View
    {
        public function         virtual( $FileName )
        {
            global          $MEZON_PATH;

            return( file_get_contents( $MEZON_PATH.'/res/templates/'.$FileName.'.tpl' ) );
        }
    }

?>