<?php

    /**
    *   Base class for all views.
    */
    class         View
    {
        /**
        *   View's generated content.
        */
        var             $Content = '';

        /**
        *   Constructor.
        */
        public function __construct( $Content )
        {
            $this->Content = $Content;
        }

        /**
        *   Method renders content from view.
        */
        public function render()
        {
            // TODO: add macro and blocks compilation here
            return( $this->Content );
        }
    }

?>