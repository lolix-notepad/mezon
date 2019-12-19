<?php
namespace Mezon\Service;

/**
 * Class ServiceClient
 *
 * @package Service
 * @subpackage ServiceClient
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/06)
 * @copyright Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../../../custom-client/custom-client.php');
require_once (__DIR__ . '/../../../dns-client/dns-client.php');

// TODO add camel-case
/**
 * Service client for Service
 */
class ServiceClient extends \Mezon\CustomClient
{

    /**
     * Service name
     */
    var $Service = '';

    /**
     * Last logged in user
     * This is used for performance improvements in ServiceClient::login_as method
     * For optimisation purposes only! Do not use in the client code
     */
    private $Login = false;

    /**
     * Session id
     *
     * @var string
     */
    protected $SessionId = false;

    /**
     * Constructor
     *
     * @param string $Service
     *            Service URL or service name
     * @param string $Login
     *            Login
     * @param string $Password
     *            Password
     * @param array $Headers
     *            Headers
     */
    public function __construct(string $Service, string $Login = '', string $Password = '', array $Headers = [])
    {
        if (\Mezon\DNS::service_exists($Service)) {
            $this->Service = $Service;
            parent::__construct(\Mezon\DNS::resolve_host($Service), $Headers);
        } elseif (strpos($Service, 'http://') === false && strpos($Service, 'https://') === false) {
            throw (new \Exception('Service ' . $Service . ' was not found in DNS'));
        } else {
            parent::__construct($Service, $Headers);
        }

        if ($Login !== '') {
            $this->connect($Login, $Password);
        }
    }

    /**
     * Method sends POST request to REST server
     *
     * @param string $Endpoint
     *            Calling endpoint
     * @param array $Data
     *            Request data
     * @return mixed Result of the request
     */
    public function post_request(string $Endpoint, array $Data = [])
    {
        $Result = parent::post_request($Endpoint, $Data);

        return (json_decode($Result));
    }

    /**
     * Method sends GET request to REST server
     *
     * @param string $Endpoint
     *            Calling endpoint
     * @return mixed Result of the remote call
     */
    public function get_request(string $Endpoint)
    {
        $Result = parent::get_request($Endpoint);

        return (json_decode($Result));
    }

    /**
     * Method connects to the REST server via login and password pair
     *
     * @param string $Login
     *            Login
     * @param string $Password
     *            Password
     */
    public function connect(string $Login, string $Password)
    {
        // authorization
        $Data = [
            'login' => $Login,
            'password' => $Password
        ];

        $Result = $this->post_request('/connect/', $Data);

        if (isset($Result->session_id) === false) {
            throw (new \Exception($Result->message, $Result->code));
        }

        $this->Login = $Login;
        $this->SessionId = $Result->session_id;
    }

    /**
     * Method sets token
     *
     * @param string $Token
     *            Access token
     * @param string $Login
     *            User login
     */
    public function set_token(string $Token, string $Login = '')
    {
        if ($Token === '') {
            throw (new \Exception('Token not set', - 4));
        }

        $this->Login = $Login;
        $this->SessionId = $Token;
    }

    /**
     * Method returns token
     *
     * @return string Session id
     */
    public function get_token(): string
    {
        return ($this->SessionId);
    }

    /**
     * Method returns self id of the session
     *
     * @return string Session user's id
     */
    public function get_self_id(): string
    {
        $Result = $this->get_request('/self/id/');

        return (isset($Result->id) ? $Result->id : $Result);
    }

    /**
     * Method returns self login of the session
     *
     * @return string Session user's login
     */
    public function get_self_login(): string
    {
        $Result = $this->get_request('/self/login/');

        return (isset($Result->login) ? $Result->login : $Result);
    }

    /**
     * Method logins under another user
     * $Field must be 'id' or 'login'
     *
     * @param string $User
     *            User credentials
     * @param string $Field
     *            Field name for credentials
     */
    public function login_as(string $User, string $Field = 'id')
    {
        if ($Field != 'id' && $this->Login !== $User) {
            $Result = $this->post_request('/login-as/', [
                $Field => $User
            ]);
            if (isset($Result->session_id) === false) {
                throw (new \Exception($Result->message, $Result->code));
            }
            $this->SessionId = $Result->session_id;
        }

        if ($Field == 'id') {
            $this->Login = false;
        } else {
            $this->Login = $User;
        }
    }

    /**
     * Method returns stored login
     *
     * @return string Stored login
     */
    public function get_stored_login()
    {
        return ($this->Login);
    }

    /**
     * Method returns common headers
     *
     * @return array Headers
     */
    protected function get_common_headers(): array
    {
        $Result = parent::get_common_headers();

        if ($this->SessionId !== false) {
            $Result[] = "Cgi-Authorization: Basic " . $this->SessionId;
        }

        return ($Result);
    }
}

?>