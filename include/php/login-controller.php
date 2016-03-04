<?php

    class           LoginController
    {
        public function         validate( $Login , $Password )
        {
            return( true );
        }

        public function         run()
        {
            if( isset( $_POST[ 'login' ] ) == false )
            {
                return;
            }

            if( $this->validate( $_POST[ 'login' ] , $_POST[ 'password' ] ) )
            {
                $_SESSION[ 'logged-in' ] = true;
            }
            else
            {
                // а тут что делаем???
            }
        }
    }

?>