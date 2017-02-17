<?php

    require_once( dirname( dirname( dirname( __FILE__ ) ) ).'/conf/conf.php' );
    require_once( MEZON_PATH.'/vendor/template-resources/template-resources.php' );

    /**
    *   Base class of the assets.
    */
    class           Asset
    {
        /**
        *   Array of CSS files.
        */
        protected $CSSFiles = [];

        /**
        *   Array of JS files.
        */
        protected $JSFiles = [];

        /**
        *   Including resources.
        */
        public function		include_files()
        {
            $TemplateResources = new TemplateResources();

            $TemplateResources->add_css_files( $this->CSSFiles );

            $TemplateResources->add_js_files( $this->JSFiles );
        }
    }

?>