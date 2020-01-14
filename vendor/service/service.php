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
    public function __construct(
        $ServiceTransport = \Mezon\Service\ServiceRestTransport::class,
        $SecurityProvider = \Mezon\Service\ServiceMockSecurityProvider::class,
        $ServiceLogic = \Mezon\Service\ServiceLogic::class,
        $ServiceModel = \Mezon\Service\ServiceModel::class)
    {
        parent::__construct($ServiceTransport, $SecurityProvider, $ServiceLogic, $ServiceModel);

        $this->initCommonRoutes();
    }

    /**
     * Method inits common servoce's routes
     */
    protected function initCommonRoutes(): void
    {
        $this->ServiceTransport->addRoute('/connect/', 'connect', 'POST', 'public_call');
        $this->ServiceTransport->addRoute('/token/[a:token]/', 'setToken', 'POST');
        $this->ServiceTransport->addRoute('/self/id/', 'getSelfId', 'GET');
        $this->ServiceTransport->addRoute('/self/login/', 'getSelfLogin', 'GET');
        $this->ServiceTransport->addRoute('/login-as/', 'loginAs', 'POST');
    }

    /**
     * Method launches service
     *
     * @param Service|string $Service
     *            name of the service class or the service object itself
     * @param Service\ServiceTransport|string $ServiceTransport
     *            name of the service transport class or the service transport itself
     * @param Service\ServiceSecurityProviderInterface|string $SecurityProvider
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
    public static function launch(
        $Service,
        $ServiceTransport = \Mezon\Service\ServiceRestTransport::class,
        $SecurityProvider = \Mezon\Service\ServiceMockSecurityProvider::class,
        $ServiceLogic = \Mezon\Service\ServiceLogic::class,
        $ServiceModel = \Mezon\Service\ServiceModel::class,
        bool $RunService = true): Service\ServiceBase
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
     * @param Service\ServiceSecurityProviderInterface|string $SecurityProvider
     *            name of the service security provider class or the service security provider itself
     * @param Service\ServiceTransport|string $ServiceTransport
     *            name of the service transport class or the service transport itself
     * @param bool $RunService
     *            Shold be service lanched
     * @return Service Created service
     */
    public static function start(
        $Service,
        $ServiceLogic = \Mezon\Service\ServiceLogic::class,
        $ServiceModel = \Mezon\Service\ServiceModel::class,
        $SecurityProvider = \Mezon\Service\ServiceMockSecurityProvider::class,
        $ServiceTransport = \Mezon\Service\ServiceRestTransport::class,
        bool $RunService = true): Service\ServiceBase
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
