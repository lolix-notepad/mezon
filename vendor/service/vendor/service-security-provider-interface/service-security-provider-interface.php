<?php
namespace Mezon\Service;

/**
 * Class ServiceSecurityProviderInterface
 *
 * @package Service
 * @subpackage ServiceSecurityProviderInterface
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/08)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Interface for security providers
 */
interface ServiceSecurityProviderInterface
{

    /**
     * Method creates session from existing token or fetched from HTTP headers
     *
     * @param string $Token
     *            Session token
     * @return string Session token
     */
    public function createSession(string $Token = ''): string;

    /**
     * Method creates conection session
     *
     * @param string $Login
     *            Login
     * @param string $Password
     *            Password
     * @return string Session id of the created session
     */
    public function connect(string $Login, string $Password): string;

    /**
     * Method sets session token
     *
     * @param string $Token
     *            Token
     * @return string Session token id
     */
    public function setToken(string $Token): string;

    /**
     * Method returns id of the session user
     *
     * @param string $Token
     *            Token
     * @return int id of the session user
     */
    public function getSelfId(string $Token): int;

    /**
     * Method returns login of the session user
     *
     * @param string $Token
     *            Token
     * @return string login of the session user
     */
    public function getSelfLogin(string $Token): string;

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
    public function loginAs(string $Token, string $LoginOrId, string $Field): string;

    /**
     * Method returns true or false if the session user has permit or not
     *
     * @param string $Token
     *            Token
     * @param string $Permit
     *            Permit name
     * @return bool True if the user has permit
     */
    public function hasPermit(string $Token, string $Permit): bool;

    /**
     * Method throws exception if the user does not have permit
     *
     * @param string $Token
     *            Token
     * @param string $Permit
     *            Permit name
     */
    public function validatePermit(string $Token, string $Permit);

    /**
     * Method returns field name for login
     *
     * @return string Field name
     */
    public function getLoginFieldName(): string;

    /**
     * Method returns field name for session_id
     *
     * @return string Field name
     */
    public function getSessionIdFieldName(): string;
}
