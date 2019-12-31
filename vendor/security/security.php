<?php
namespace Mezon;

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
     * Returning string value
     *
     * @param string $Value
     *            Value to be made secure
     * @return string Secure value
     */
    public static function getStringValue(string $Value): string
    {
        if ($Value == '""') {
            return ('');
        } else {
            return (htmlspecialchars($Value));
        }
    }

    /**
     * Method prepares file system for saving file
     *
     * @param string $FilePrefix
     *            Prefix to file path
     * @return string File path
     */
    private static function prepareFs(string $FilePrefix): string
    {
        @mkdir($FilePrefix . '/data/');

        $Path = '/data/files/';

        @mkdir($FilePrefix . $Path);

        @mkdir($FilePrefix . $Path . date('Y') . '/');

        @mkdir($FilePrefix . $Path . date('Y') . '/' . date('m') . '/');

        $Dir = $Path . date('Y') . '/' . date('m') . '/' . date('d') . '/';

        @mkdir($FilePrefix . $Dir);

        return ($Dir);
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
        if (is_array($Value) === false) {
            $Value = $_FILES[$Value];
        }

        if (isset($Value['size']) && $Value['size'] === 0) {
            return ('');
        }

        if ($StoreFiles) {
            $Dir = '.' . self::prepareFs('.');

            $UploadFile = $Dir . md5($Value['name'] . microtime(true)) . '.' . pathinfo($Value['name'], PATHINFO_EXTENSION);

            if (isset($Value['file'])) {
                file_put_contents($UploadFile, base64_decode($Value['file']));
            } else {
                move_uploaded_file($Value['tmp_name'], $UploadFile);
            }

            return ($UploadFile);
        } else {
            return ($Value);
        }
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
        $Dir = self::prepareFs($PathPrefix);

        $FileName = md5(microtime(true));

        if ($Decoded) {
            file_put_contents($PathPrefix . $Dir . $FileName, $FileContent);
        } else {
            file_put_contents($PathPrefix . $Dir . $FileName, base64_decode($FileContent));
        }

        return ($Dir . $FileName);
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
        $FileContent = @file_get_contents($FilePath);

        if ($FileContent === false) {
            return (null);
        }

        return (self::storeFileContent($FileContent, $PathPrefix, $Decoded));
    }
}

?>