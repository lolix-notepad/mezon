<?php
/**
 * Class Service
 *
 * @package     Mezon
 * @subpackage  Service
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/08/17)
 * @copyright   Copyright (c) 2019, aeon.org
 */
require_once(__DIR__.'/../service-base/service-base.php');

/**
 * Service class
 * 
 * It bounds together transport, request parameters fetcher, logic, authorization and model
 * 
 * @author Dodonov A.A.
 */
class Service extends ServiceBase
{

	/**
	 * Constructor
	 *
	 * @param mixed $ServiceTransport Service's transport
	 * @param mixed $SecurityProvider Service's security provider
	 * @param mixed $ServiceLogic Service's logic
	 * @param mixed $ServiceModel Service's model
	 */
	public function __construct($ServiceTransport = 'ServiceRESTTransport', $SecurityProvider = 'ServiceMockSecurityProvider', $ServiceLogic = 'ServiceLogic', $ServiceModel = 'ServiceModel')
	{
	    parent::__construct($ServiceTransport, $SecurityProvider, $ServiceLogic, $ServiceModel);

	    $this->init_common_routes();
	}

	/**
	 * Method inits common servoce's routes
	 */
	protected function init_common_routes():void
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
	 * @param Service|string $Service name of the service class or the service object itself
	 * @param ServiceTransport|string $ServiceTransport name of the service transport class or the service transport itself
	 * @param ServiceSecurityProvider|string $SecurityProvider name of the service security provider class or the service security provider itself
	 * @param ServiceLogic|string $ServiceLogic Logic of the service
	 * @param ServiceModel|string $ServiceModel Model of the service
	 * @param bool $RunService Shold be service lanched
	 * @return Service Created service
	 * @deprecated See Service::run
	 */
	public static function launch($Service, $ServiceTransport = 'ServiceRESTTransport', $SecurityProvider = 'ServiceMockSecurityProvider', $ServiceLogic = 'ServiceLogic', $ServiceModel = 'ServiceModel', bool $RunService = true): ServiceBase
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
	 * @param Service|string $Service name of the service class or the service object itself
	 * @param ServiceLogic|string $ServiceLogic Logic of the service
	 * @param ServiceModel|string $ServiceModel Model of the service
	 * @param ServiceSecurityProvider|string $SecurityProvider name of the service security provider class or the service security provider itself
	 * @param ServiceTransport|string $ServiceTransport name of the service transport class or the service transport itself
	 * @param bool $RunService Shold be service lanched
	 * @return Service Created service
	 */
	public static function start($Service, $ServiceLogic = 'ServiceLogic', $ServiceModel = 'ServiceModel', $SecurityProvider = 'ServiceMockSecurityProvider', $ServiceTransport = 'ServiceRESTTransport', bool $RunService = true): ServiceBase
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