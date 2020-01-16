<?php
namespace Mezon\Service;

/**
 * Class ServiceMockSecurityProvider
 *
 * @package Service
 * @subpackage ServiceMockSecurityProvider
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/06)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Class ServiceMockSecurityProvider - provides mockes for all security methods
 */
class ServiceMockSecurityProvider implements \Mezon\Service\ServiceSecurityProviderInterface
{

    /**
     * Method creates session from existing token or fetched from HTTP headers
     *
     * @param string $Token
     *            Session token
     * @return string Session token
     */
    public function createSession(string $Token = null): string
    {
        if ($Token === null) {
            return (md5(microtime(true)));
        } else {
            return ($Token);
        }
    }

    /**
     * Method creates conection session
     *
     * @param string $Login
     *            Login
     * @param string $Password
     *            Password
     * @return string Session id of the created session
     */
    public function connect(string $Login, string $Password): string
    {
        return (md5(microtime(true)));
    }

    /**
     * Method sets session token
     *
     * @param string $Token
     *            Token
     * @return string Session token id
     */
    public function setToken(string $Token): string
    {
        return ($Token);
    }

    /**
     * Method returns id of the session user
     *
     * @param string $Token
     *            Token
     * @return int id of the session user
     */
    public function getSelfId(string $Token): int
    {
        return (1);
    }

    /**
     * Method returns login of the session user
     *
     * @param string $Token
     *            Token
     * @return string login of the session user
     */
    public function getSelfLogin(string $Token): string
    {
        return ('admin@localhost');
    }

    /**
     * Method allows user to login under another user
     *
     * @param string $Token
     *            Token
     * @param string $LoginOrId
     *            In this field login or user id are passed
     * @param string $Field
     *            Contains 'login' or 'id'
     * @return string New session id
     */
    public function loginAs(string $Token, string $LoginOrId, string $Field): string
    {
        return ($Token);
    }

    /**
     * Method returns true or false if the session user has permit or not
     *
     * @param string $Token
     *            Token
     * @param string $Permit
     *            Permit name
     * @return bool True if the
     */
    public function hasPermit(string $Token, string $Permit): bool
    {
        return (true);
    }

    /**
     * Method throws exception if the user does not have permit
     *
     * @param string $Token
     *            Token
     * @param string $Permit
     *            Permit name
     */
    public function validatePermit(string $Token, string $Permit)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Mezon\Service\ServiceSecurityProviderInterface::getLoginFieldName()
     */
    public function getLoginFieldName(): string
    {
        return ('login');
    }

    /**
     * Method returns field name for session_id
     *
     * @return string Field name
     */
    public function getSessionIdFieldName(): string
    {
        return ('session_id');
    }
}
