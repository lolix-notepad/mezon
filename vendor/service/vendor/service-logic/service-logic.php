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
        $Login = $this->getParam($this->SecurityProvider->getLoginFieldName(), false);
        $Password = $this->getParam('password', false);

        if ($Login === false || $Password === false) {
            throw (new \Exception('Fields login and/or password were not set', - 1));
        }

        return ([
            $this->SecurityProvider->getSessionIdFieldName() => $this->SecurityProvider->connect($Login, $Password)
        ]);
    }

    /**
     * Method sets token
     *
     * @return array Session id
     */
    public function setToken(): array
    {
        return ([
            $this->SecurityProvider->getSessionIdFieldName() => $this->SecurityProvider->setToken($this->getParam('token'))
        ]);
    }

    /**
     * Method returns session user's id
     *
     * @return integer Session user's id
     */
    public function getSelfId(): array
    {
        return ([
            'id' => $this->getSelfIdValue()
        ]);
    }

    /**
     * Method returns session user's login
     *
     * @return string Session user's login
     */
    public function getSelfLogin(): array
    {
        return ([
            $this->SecurityProvider->getLoginFieldName() => $this->getSelfLoginValue()
        ]);
    }

    /**
     * Method returns session id
     *
     * @return string Session id
     */
    protected function getSessionId(): string
    {
        return ($this->getParam($this->SecurityProvider->getSessionIdFieldName()));
    }

    /**
     * Method allows to login under another user
     *
     * @return array Session id
     */
    public function loginAs(): array
    {
        $LoginFieldName = $this->SecurityProvider->getLoginFieldName();

        // we can login using either user's login or id
        if (($LoginOrId = $this->getParam($LoginFieldName, '')) !== '') {
            // we are log in using login
            $LoginFieldName = 'login';
        } elseif (($LoginOrId = $this->getParam('id', '')) !== '') {
            // we are log in using id
            $LoginFieldName = 'id';
        }

        return ([
            $this->SecurityProvider->getSessionIdFieldName() => $this->SecurityProvider->loginAs($this->getSessionId(), $LoginOrId, $LoginFieldName)
        ]);
    }

    /**
     * Method returns self id
     *
     * @return integer Session user's id
     */
    public function getSelfIdValue(): int
    {
        return ($this->SecurityProvider->getSelfId($this->getSessionId()));
    }

    /**
     * Method returns self login
     *
     * @return string Session user's login
     */
    public function getSelfLoginValue(): string
    {
        return ($this->SecurityProvider->getSelfLogin($this->getSessionId()));
    }

    /**
     * Checking does user has permit
     *
     * @param string $Permit
     *            Permit to check
     * @return bool true or false if the session user has permit or not
     */
    public function hasPermit(string $Permit): bool
    {
        return ($this->SecurityProvider->hasPermit($this->getSessionId(), $Permit));
    }

    /**
     * The same as hasPermit but throwing exception for session user no permit
     *
     * @param string $Permit
     *            Permit name
     */
    public function validatePermit(string $Permit)
    {
        $this->SecurityProvider->validatePermit($this->getSessionId(), $Permit);
    }
}

?>