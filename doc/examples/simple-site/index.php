<?php

    require_once( '../../../mezon.php' );

    class           SiteApplication extends Application
    {
        /**
        *   Main page.
        */
        public function action_index()
        {
            return( 'This the main page of our simple site!' );
        }

        /**
        *   Contacts page.
        */
        public function action_contacts()
        {
            return( 'This the "Contacts" page' );
        }
    }

    $App = new SiteApplication();
    $App->run();

?>