<?php
require_once (__DIR__ . '/../../../../functional/functional.php');
require_once (__DIR__ . '/../../../../router/router.php');

require_once (__DIR__ . '/../../../../service/vendor/service-logic/service-logic.php');
require_once (__DIR__ . '/../../../../service/vendor/service-logic/vendor/service-logic-unit-tests/service-logic-unit-tests.php');
require_once (__DIR__ . '/../../../../service/vendor/service-console-transport/service-console-transport.php');
require_once (__DIR__ . '/../../../../service/vendor/service-console-transport/vendor/console-request-params/console-request-params.php');
require_once (__DIR__ . '/../../../../service/vendor/service-security-provider/service-security-provider.php');
require_once (__DIR__ . '/../../../../service/vendor/service-mock-security-provider/service-mock-security-provider.php');

require_once (__DIR__ . '/../vendor/crud-service-logic-unit-tests/crud-service-logic-unit-tests.php');

/**
 * CRUD service logic unit tests.
 * 
 * @author Dodonov A.A.
 */
class CRUDServiceLogicUnitTest extends CRUDServiceLogicUnitTests
{
}

?>