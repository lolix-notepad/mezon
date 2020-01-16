<?php
namespace Mezon\Security;

/**
 * Class Security
 *
 * @package Mezon
 * @subpackage Security
 * @author Dodonov A.A.
 * @version v.1.0 (2019/10/08)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Security class
 *
 * @author Dodonov A.A.
 */
class Security
{

    /**
     * Security rules
     *
     * @var \Mezon\Security\SecurityRules\SecurityRules
     */
    public static $SecurityRules = null;

    /**
     * Method returns security rules
     *
     * @return \Mezon\Security\SecurityRules\SecurityRules
     */
    public static function getSecurityRules(): \Mezon\Security\SecurityRules\SecurityRules
    {
        if (self::$SecurityRules === null) {
            self::$SecurityRules = new \Mezon\Security\SecurityRules\SecurityRules();
        }

        return (self::$SecurityRules);
    }

    /**
     * Returning string value
     *
     * @param string $Value
     *            Value to be made secure
     * @return string Secure value
     */
    public static function getStringValue(string $Value): string
    {
        return (self::getSecurityRules()->getStringValue($Value));
    }

    /**
     * Method returns file value
     *
     * @param mixed $Value
     *            Data about the uploaded file
     * @param bool $StoreFiles
     *            Must be the file stored in the file system of the service or not
     * @return string|array Path to the stored file or the array $Value itself
     */
    public static function getFileValue($Value, bool $StoreFiles)
    {
        return (self::getSecurityRules()->getFileValue($Value, $StoreFiles));
    }

    /**
     * Method stores file on disk
     *
     * @param string $FileContent
     *            Content of the saving file
     * @param string $PathPrefix
     *            Prefix to file
     * @param bool $Decoded
     *            If the file was not encodded in base64
     * @return string Path to file
     */
    public static function storeFileContent(string $FileContent, string $PathPrefix, bool $Decoded = false): string
    {
        return (self::getSecurityRules()->storeFileContent($FileContent, $PathPrefix, $Decoded));
    }

    /**
     * Method stores file on disk
     *
     * @param string $FilePath
     *            Path to the saving file
     * @param string $PathPrefix
     *            Prefix to file
     * @param bool $Decoded
     *            If the file was not encodded in base64
     * @return string Path to file or null if the image was not loaded
     */
    public static function storeFile(string $FilePath, string $PathPrefix, bool $Decoded = false): ?string
    {
        return (self::getSecurityRules()->storeFile($FilePath, $PathPrefix, $Decoded));
    }
}
