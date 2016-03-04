<?php

    /**
    *   Class displays buttons on main page.
    */
    class           MainPageButtonsView
    {
        public static           $Instance = false;

        public static           $MainPageButtons = false;

        /**
        *   Constructor creates singleton.
        */
        function __construct()
        {
            if( self::$Instance === false )
            {
                self::$Instance = $this;
            }

            if( self::$MainPageButtons === false )
            {
                self::$MainPageButtons = array( /*'settings' => true , */ 'exit' => true );
            }
        }

        /**
        *   Компиляция простой кнопки.
        */
        public function         compile_button( $ButtonName , $ButtonLabel )
        {
            global          $MEZON_PATH;

            $Content = file_get_contents( $MEZON_PATH.'/res/templates/main-page-button.tpl' );
            $Content = str_replace( '{button-name}' , $ButtonName , $Content );
            $Content = str_replace( '{button-label}' , $ButtonLabel , $Content );

            return( $Content );
        }

        /**
        *   Компиляция кнопки, которая открывает таб со списком сущностей.
        */
        public function         compile_entity_button( $Button )
        {
            global          $MEZON_PATH;

            $Content = file_get_contents( $MEZON_PATH.'/res/templates/entity-button.tpl' );

            $Content = str_replace( '{button-name}' , $Button[ 'button-name' ] , $Content );
            $Content = str_replace( '{button-label}' , $Button[ 'button-label' ] , $Content );
            $Content = str_replace( '{button-view}' , $Button[ 'button-view' ] , $Content );

            return( $Content );
        }

        /**
        *   Генерация тулбара для главной страницы.
        */
        public function         run()
        {
            global          $MEZON_PATH;
            $Content = '';

            foreach( self::$MainPageButtons as $Key => $Button )
            {
                switch( $Key )
                {
                    case( 'settings' ): $Content .= $this->compile_button( $Key , 'Настройки системы' ); break;
                    case( 'exit' ): $Content .= $this->compile_button( $Key , 'Выход из системы' ); break;
                    case( 'entity-button' ): $Content .= $this->compile_entity_button( $Button ); break;
                    default: $Content .= $this->compile_button( $Key , $Button[ 'label' ] ); break;
                }
            }

            return( 
                file_get_contents( $MEZON_PATH.'/res/templates/main-page-buttons-toolbar-start.tpl' ).$Content.
                file_get_contents( $MEZON_PATH.'/res/templates/main-page-buttons-toolbar-end.tpl' )
            );
        }
    }

?>