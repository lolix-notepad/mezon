<?php

    require_once( dirname( __FILE__ ).'/conf/conf.php' );

	require_once( $MEZON_PATH.'/vendor/application/application.php' );
	require_once( $MEZON_PATH.'/vendor/basic-application/basic-application.php' );
	require_once( $MEZON_PATH.'/vendor/basic-auth/basic-auth.php' );
	require_once( $MEZON_PATH.'/vendor/basic-template/basic-template.php' );
	require_once( $MEZON_PATH.'/vendor/call-cache/call-cache.php' );
	require_once( $MEZON_PATH.'/vendor/functional/functional.php' );

    //TODO: virtual routes like in Klein router (or Yii)
    //TODO: implement class lookup with name 'class_name' in %mezon-path%/vendor/bundle-name for routes /bundle/class/action/ + tests
    //TODO: illegal routes must return 404 code but not output exception description + tests

?>