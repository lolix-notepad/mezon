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
     * This is used for performance improvements in ServiceClient::loginAs method
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
        if (\Mezon\DNS::serviceExists($Service)) {
            $this->Service = $Service;
            parent::__construct(\Mezon\DNS::resolveHost($Service), $Headers);
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
    public function postRequest(string $Endpoint, array $Data = [])
    {
        $Result = parent::postRequest($Endpoint, $Data);

        return (json_decode($Result));
    }

    /**
     * Method sends GET request to REST server
     *
     * @param string $Endpoint
     *            Calling endpoint
     * @return mixed Result of the remote call
     */
    public function getRequest(string $Endpoint)
    {
        $Result = parent::getRequest($Endpoint);

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

        $Result = $this->postRequest('/connect/', $Data);

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
    public function setToken(string $Token, string $Login = '')
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
    public function getToken(): string
    {
        return ($this->SessionId);
    }

    /**
     * Method returns self id of the session
     *
     * @return string Session user's id
     */
    public function getSelfId(): string
    {
        $Result = $this->getRequest('/self/id/');

        return (isset($Result->id) ? $Result->id : $Result);
    }

    /**
     * Method returns self login of the session
     *
     * @return string Session user's login
     */
    public function getSelfLogin(): string
    {
        $Result = $this->getRequest('/self/login/');

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
    public function loginAs(string $User, string $Field = 'id')
    {
        if ($Field != 'id' && $this->Login !== $User) {
            $Result = $this->postRequest('/login-as/', [
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
    public function getStoredLogin()
    {
        return ($this->Login);
    }

    /**
     * Method returns common headers
     *
     * @return array Headers
     */
    protected function getCommonHeaders(): array
    {
        $Result = parent::getCommonHeaders();

        if ($this->SessionId !== false) {
            $Result[] = "Cgi-Authorization: Basic " . $this->SessionId;
        }

        return ($Result);
    }
}

?>