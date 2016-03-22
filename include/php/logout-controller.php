<?php

    //TODO: move to vendor
    class           LogoutController
    {
        public function         run()
        {
            //unset( $_SESSION[ 'logged-in' ] );
            $_SESSION[ 'logged-in' ] = false;
            header( 'Location: ./' );
            exit(0);
        }
    }

?>