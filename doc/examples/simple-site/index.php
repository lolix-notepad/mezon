<?php

    require_once( '../../../mezon.php' );

    class           SiteApplication extends Application
    {
        /**
        *   Main page.
        */
        public function action_index()
        {
            return( 'This is the main page of our simple site' );
        }

        /**
        *   Contacts page.
        */
        public function action_contacts()
        {
            return( 'This is the "Contacts" page' );
        }
    }

    $App = new SiteApplication();
    $App->run();

?>