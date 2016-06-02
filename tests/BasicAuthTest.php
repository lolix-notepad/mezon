<?php

    global          $MEZON_PATH;
    require_once( $MEZON_PATH.'/vendor/basic-auth/basic-auth.php' );

    class   BasicAuthPublic extends BasicAuth
    {
        public function			__construct( $Realm , $UserSet )
		{
			parent::__construct( $Realm , $UserSet );
		}

        public function validate_login_pub()
        {
            return( $this->validate_login() );
        }

        public function validate_password_pub()
        {
            return( $this->validate_password() );
        }
    }
 
    class BasicAuthTest extends PHPUnit_Framework_TestCase
    {
        /**
        *   Testing user validation.
        */
        public function testValidateValidLoginSingleUser()
        {
            $Auth = new BasicAuthPublic( 'Admin console' , array( 'login' =>'admin' , 'password' => '1234567' ) );

            $_SERVER[ 'PHP_AUTH_USER' ] = 'admin';

            $this->assertEquals( $Auth->validate_login_pub() , true , 'Invalid login validation' );
        }

        /**
        *   Testing user validation.
        */
        public function testValidateInValidLoginSingleUser()
        {
            $Auth = new BasicAuthPublic( 'Admin console' , array( 'login' =>'admin' , 'password' => '1234567' ) );

            $_SERVER[ 'PHP_AUTH_USER' ] = 'admin2';

            $this->assertEquals( $Auth->validate_login_pub() , false , 'Invalid login validation' );
        }

        /**
        *   Testing user validation.
        */
        public function testValidateValidLoginManyUsers()
        {
            $Auth = new BasicAuthPublic( 
                'Admin console' , 
                array( 
                    array( 'login' =>'admin' , 'password' => '1234567' ) , 
                    array( 'login' => 'manager' , 'password' => '7654321' )
                )
            );

            $_SERVER[ 'PHP_AUTH_USER' ] = 'manager';

            $this->assertEquals( $Auth->validate_login_pub() , true , 'Invalid login validation' );
        }

        /**
        *   Testing user validation.
        */
        public function testValidateInValidLoginManyUsers()
        {
            $Auth = new BasicAuthPublic( 
                'Admin console' , 
                array( 
                    array( 'login' =>'admin' , 'password' => '1234567' ) , 
                    array( 'login' => 'manager' , 'password' => '7654321' )
                )
            );

            $_SERVER[ 'PHP_AUTH_USER' ] = 'manager1234';

            $this->assertEquals( $Auth->validate_login_pub() , false , 'Invalid login validation' );
        }
        
        /**
        *   Testing user validation.
        */
        public function testValidateValidPasswordSingleUser()
        {
            $Auth = new BasicAuthPublic( 'Admin console' , array( 'login' =>'admin' , 'password' => '1234567' ) );

            $_SERVER[ 'PHP_AUTH_USER' ] = 'admin';
            $_SERVER[ 'PHP_AUTH_PW' ] = '1234567';

            $this->assertEquals( $Auth->validate_password_pub() , true , 'Invalid password validation' );
        }

        /**
        *   Testing user validation.
        */
        public function testValidateInValidPasswordSingleUser()
        {
            $Auth = new BasicAuthPublic( 'Admin console' , array( 'login' =>'admin' , 'password' => '1234567' ) );

            $_SERVER[ 'PHP_AUTH_USER' ] = 'admin';
            $_SERVER[ 'PHP_AUTH_PW' ] = '7654321';

            $this->assertEquals( $Auth->validate_password_pub() , false , 'Invalid password validation' );
        }

        /**
        *   Testing user validation.
        */
        public function testValidateValidPasswordManyUsers()
        {
            $Auth = new BasicAuthPublic( 
                'Admin console' , 
                array( 
                    array( 'login' =>'admin' , 'password' => '1234567' ) , 
                    array( 'login' => 'manager' , 'password' => '7654321' )
                )
            );

            $_SERVER[ 'PHP_AUTH_USER' ] = 'manager';
            $_SERVER[ 'PHP_AUTH_PW' ] = '7654321';

            $this->assertEquals( $Auth->validate_password_pub() , true , 'Invalid password validation' );
        }

        /**
        *   Testing user validation.
        */
        public function testValidateInValidPasswordManyUsers()
        {
            $Auth = new BasicAuthPublic( 
                'Admin console' , 
                array( 
                    array( 'login' =>'admin' , 'password' => '1234567' ) , 
                    array( 'login' => 'manager' , 'password' => '7654321' )
                )
            );

            $_SERVER[ 'PHP_AUTH_USER' ] = 'manager';
            $_SERVER[ 'PHP_AUTH_PW' ] = '0000000';

            $this->assertEquals( $Auth->validate_password_pub() , false , 'Invalid password validation' );
        }
    }

?>