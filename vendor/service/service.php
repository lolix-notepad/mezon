<?php
namespace Mezon;

/**
 * Class Service
 *
 * @package Mezon
 * @subpackage Service
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/17)
 * @copyright Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/vendor/service-base/service-base.php');

// TODO add camel-case
/**
 * Service class
 *
 * It bounds together transport, request parameters fetcher, logic, authorization and model
 *
 * @author Dodonov A.A.
 */
class Service extends Service\ServiceBase
{

    /**
     * Constructor
     *
     * @param mixed $ServiceTransport
     *            Service's transport
     * @param mixed $SecurityProvider
     *            Service's security provider
     * @param mixed $ServiceLogic
     *            Service's logic
     * @param mixed $ServiceModel
     *            Service's model
     */
    public function __construct($ServiceTransport = 'Mezon\Service\ServiceRESTTransport', $SecurityProvider = 'Mezon\Service\ServiceMockSecurityProvider', $ServiceLogic = 'Mezon\Service\ServiceLogic', $ServiceModel = 'Mezon\Service\ServiceModel')
    {
        parent::__construct($ServiceTransport, $SecurityProvider, $ServiceLogic, $ServiceModel);

        $this->init_common_routes();
    }

    /**
     * Method inits common servoce's routes
     */
    protected function init_common_routes(): void
    {
        $this->ServiceTransport->add_route('/connect/', 'connect', 'POST', 'public_call');
        $this->ServiceTransport->add_route('/token/[a:token]/', 'set_token', 'POST');
        $this->ServiceTransport->add_route('/self/id/', 'get_self_id', 'GET');
        $this->ServiceTransport->add_route('/self/login/', 'get_self_login', 'GET');
        $this->ServiceTransport->add_route('/login-as/', 'login_as', 'POST');
    }

    /**
     * Method launches service
     *
     * @param Service|string $Service
     *            name of the service class or the service object itself
     * @param Service\ServiceTransport|string $ServiceTransport
     *            name of the service transport class or the service transport itself
     * @param Service\ServiceSecurityProvider|string $SecurityProvider
     *            name of the service security provider class or the service security provider itself
     * @param Service\ServiceLogic|string $ServiceLogic
     *            Logic of the service
     * @param Service\ServiceModel|string $ServiceModel
     *            Model of the service
     * @param bool $RunService
     *            Shold be service lanched
     * @return Service Created service
     * @deprecated See Service::run
     */
    public static function launch($Service, $ServiceTransport = '\Mezon\Service\ServiceRESTTransport', $SecurityProvider = '\Mezon\Service\ServiceMockSecurityProvider', $ServiceLogic = '\Mezon\Service\ServiceLogic', $ServiceModel = '\Mezon\Service\ServiceModel', bool $RunService = true): Service\ServiceBase
    {
        if (is_string($Service)) {
            $Service = new $Service($ServiceTransport, $SecurityProvider, $ServiceLogic, $ServiceModel);
        }

        if ($RunService === false) {
            return ($Service);
        }

        $Service->run();

        return ($Service);
    }

    /**
     * Method launches service
     *
     * @param Service|string $Service
     *            name of the service class or the service object itself
     * @param Service\ServiceLogic|string $ServiceLogic
     *            Logic of the service
     * @param Service\ServiceModel|string $ServiceModel
     *            Model of the service
     * @param Service\ServiceSecurityProvider|string $SecurityProvider
     *            name of the service security provider class or the service security provider itself
     * @param Service\ServiceTransport|string $ServiceTransport
     *            name of the service transport class or the service transport itself
     * @param bool $RunService
     *            Shold be service lanched
     * @return Service Created service
     */
    public static function start($Service, $ServiceLogic = '\Mezon\Service\ServiceLogic', $ServiceModel = '\Mezon\Service\ServiceModel', $SecurityProvider = '\Mezon\Service\ServiceMockSecurityProvider', $ServiceTransport = '\Mezon\Service\ServiceRESTTransport', bool $RunService = true): Service\ServiceBase
    {
        if (is_string($Service)) {
            $Service = new $Service($ServiceTransport, $SecurityProvider, $ServiceLogic, $ServiceModel);
        }

        if ($RunService === false) {
            return ($Service);
        }

        $Service->run();

        return ($Service);
    }
}

?>