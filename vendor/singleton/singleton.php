<?php

    /**
    *   Singleton class.
    */
    class           Singleton
    {
        /**
        *   Created instances of different objects.
        */
        private static $Instances;

        /**
        *   Constructor.
        */
        public function __construct()
        {
            $ClassName = get_class( $this );

            if( isset( self::$Instances[ $ClassName ] ) )
            {
                throw( new Exception( "You can not create more than one copy of a singleton of type $ClassName" ) );
            }
            else
            {
                self::$Instances[ $ClassName ] = $this;
            }
        }

        /**
        *   Function returns instance of the object.
        */
        public function get_instance()
        {
            $ClassName = get_called_class();

            if( !isset( self::$Instances[ $ClassName ] ) )
            {
                $Args = func_get_args();

                $ReflectionObject = new ReflectionClass( $ClassName );

                self::$Instances[ $ClassName ] = $ReflectionObject->newInstanceArgs( $Args );
            }

            return( self::$Instances[ $ClassName ] );
        }

        /**
        *   Cloner.
        */
        public function __clone()
        {
            throw( new Exception( 'You can not clone a singleton.' ) );
        }

        /**
        *   Destroy object.
        */
        public function destroy()
        {
            $ClassName = get_called_class();

            if( isset( self::$Instances[ $ClassName ] ) )
            {
                unset( self::$Instances[ $ClassName ] );
            }
        }
    }

?>