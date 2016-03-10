<?php

    class           IndexView extends View
    {
        public function         run()
        {
            global          $MEZON_PATH;

            $Engine = new TemplateEngine();
            $Engine->set_default_page_vars();
            $Engine->set_page_var( 'title' , 'Главная' );

            if( @$_SESSION[ 'logged-in' ] )
            {
                $Content = file_get_contents( $MEZON_PATH.'/res/templates/main-page.tpl' );
            }
            else
            {
                $Content = file_get_contents( $MEZON_PATH.'/res/templates/login-page.tpl' );
            }

            return( $Content );
        }
    }

?>