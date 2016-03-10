<?php

    require_once( '../../../mezon.php' );

    class           HelloWorldoApplication extends Application
    {
        /**
        *   Main page.
        */
        public function action_index()
        {
            return( 'Hello world!' );
        }
    }

    $App = new HelloWorldoApplication();
    $App->run();

?>