<?php
namespace Mezon\Service;

/**
 * Class ServiceLogic
 *
 * @package Service
 * @subpackage ServiceLogic
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/17)
 * @copyright Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../service-base-logic/service-base-logic.php');
require_once (__DIR__ . '/../service-model/service-model.php');

// TODO add camel-case
/**
 * Class stores all service's logic
 *
 * @author Dodonov A.A.
 */
class ServiceLogic extends \Mezon\Service\ServiceBaseLogic
{

    /**
     * Method creates connection
     *
     * @return array session id
     */
    public function connect(): array
    {
        $Login = $this->get_param($this->SecurityProvider->get_login_field_name(), false);
        $Password = $this->get_param('password', false);

        if ($Login === false || $Password === false) {
            throw (new \Exception('Fields login and/or password were not set', - 1));
        }

        return ([
            $this->SecurityProvider->get_session_id_field_name() => $this->SecurityProvider->connect($Login, $Password)
        ]);
    }

    /**
     * Method sets token
     *
     * @return array Session id
     */
    public function set_token(): array
    {
        return ([
            $this->SecurityProvider->get_session_id_field_name() => $this->SecurityProvider->set_token($this->get_param('token'))
        ]);
    }

    /**
     * Method returns session user's id
     *
     * @return integer Session user's id
     */
    public function get_self_id(): array
    {
        return ([
            'id' => $this->get_self_id_value()
        ]);
    }

    /**
     * Method returns session user's login
     *
     * @return string Session user's login
     */
    public function get_self_login(): array
    {
        return ([
            $this->SecurityProvider->get_login_field_name() => $this->get_self_login_value()
        ]);
    }

    /**
     * Method returns session id
     *
     * @return string Session id
     */
    protected function get_session_id(): string
    {
        return ($this->get_param($this->SecurityProvider->get_session_id_field_name()));
    }

    /**
     * Method allows to login under another user
     *
     * @return array Session id
     */
    public function login_as(): array
    {
        $LoginFieldName = $this->SecurityProvider->get_login_field_name();

        // we can login using either user's login or id
        if (($LoginOrId = $this->get_param($LoginFieldName, '')) !== '') {
            // we are log in using login
            $LoginFieldName = 'login';
        } elseif (($LoginOrId = $this->get_param('id', '')) !== '') {
            // we are log in using id
            $LoginFieldName = 'id';
        }

        return ([
            $this->SecurityProvider->get_session_id_field_name() => $this->SecurityProvider->login_as($this->get_session_id(), $LoginOrId, $LoginFieldName)
        ]);
    }

    /**
     * Method returns self id
     *
     * @return integer Session user's id
     */
    public function get_self_id_value(): int
    {
        return ($this->SecurityProvider->get_self_id($this->get_session_id()));
    }

    /**
     * Method returns self login
     *
     * @return string Session user's login
     */
    public function get_self_login_value(): string
    {
        return ($this->SecurityProvider->get_self_login($this->get_session_id()));
    }

    /**
     * Checking does user has permit
     *
     * @param string $Permit
     *            Permit to check
     * @return bool true or false if the session user has permit or not
     */
    public function has_permit(string $Permit): bool
    {
        return ($this->SecurityProvider->has_permit($this->get_session_id(), $Permit));
    }

    /**
     * The same as has_permit but throwing exception for session user no permit
     *
     * @param string $Permit
     *            Permit name
     */
    public function validate_permit(string $Permit)
    {
        $this->SecurityProvider->validate_permit($this->get_session_id(), $Permit);
    }
}

?>