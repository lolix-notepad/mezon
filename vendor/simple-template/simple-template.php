<?php

    global          $MEZON_PATH;
    require_once( $MEZON_PATH.'/vendor/template-engine/template-engine.php' );

    /**
    *   Class of the simple template.
    */
    class           SimpleTemplate
    {
        /**
        *   Template Engine.
        */
        private         $TemplateEingine = false;

        /**
        *   Singleton ñonstructor.
        */
        function __construct()
        {
            if( self::$Instance === false )
            {
                self::$Instance = $this;
            }

            // getting application's actions
            $this->Router = new Router();
            $this->Router->fetch_actions( $this );
        }
    }

?>