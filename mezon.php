<?php

    require_once( dirname( __FILE__ ).'/conf/conf.php' );

    require_once( $MEZON_PATH.'/include/php/application.php' );
    require_once( $MEZON_PATH.'/include/php/main-page-buttons-view.php' );
    require_once( $MEZON_PATH.'/include/php/custom-resources-view.php' );

    //TODO: map all 'action_*' class methods on single item routes, that allows to remove $Object in Router::parse_route and Router::call_route methods
    //TODO: router class, move all methods inside it
    //TODO: implement class lookup with name 'class_name' in %mezon-path%/vendor/bundle-name for routes /bundle/class/action/ + tests
    //TODO: 3-component routes /bundle-name/class-name/action-name/ + tests
    //TODO: virtual routes like in Klein router (or Yii)
    //TODO: multiple vendor paths + tests
    //TODO: illegal routes must return 404 code but not output exception description + tests

?>