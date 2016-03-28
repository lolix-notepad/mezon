<?php

    require_once( '../../../mezon.php' );

    function        sitemap()
    {
        return( 'Some fake sitemap' );
    }

    /**
    *   Note that here we don't extend Application class.
    */
    class           MySite
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

        /**
        *   Some custom action handler.
        */
        public function some_other_page()
        {
            return( 'Some other page of our site' );
        }
    }

    $Router = new Router();
    $Router->fetch_actions( $MySite = new MySite() );

    // here we can call
    print( $Router->call_route( '/index/' ).'<br><br>' );
    // or
    print( $Router->call_route( '/contacts/' ).'<br><br>' );
    // but this call 
    // $Router->call_route( '/some_other_page/' );
    // will throw exception
    // methods without prefix 'action_' are not automatically fetched

    $Router->add_route( 'some_any_other_route' , array( $MySite , 'some_other_page' ) ); // that is also OK
    print( $Router->call_route( 'some_any_other_route' ).'<br><br>' );

    $Router->add_route( 'sitemap' , 'sitemap' ); // that is OK
    print( $Router->call_route( 'sitemap' ).'<br><br>' );

?>