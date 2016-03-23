<?php

    require_once( dirname( __FILE__ ).'/conf/conf.php' );

    require_once( $MEZON_PATH.'/include/php/application.php' );
    require_once( $MEZON_PATH.'/include/php/main-page-buttons-view.php' );
    require_once( $MEZON_PATH.'/include/php/custom-resources-view.php' );

    //TODO: project in tree + KPI + test coverage )
    //TODO: multiple vendor paths
    //TODO: implement class lookup with name 'class_name' in %mezon-path%/vendor/bundle-name for routes /bundle/class/action/
    //TODO: illegal routes must return 404 code but not output exception description
    //TODO: 3-component routes /bundle-name/class-name/action-name/

?>