<?php
namespace Mezon\Security\SecurityRules;

/**
 * Class SecurityRules
 *
 * @package Security
 * @subpackage SecurityRules
 * @author Dodonov A.A.
 * @version v.1.0 (2020/01/13)
 * @copyright Copyright (c) 2020, aeon.org
 */

/**
 * Security rules class
 *
 * @author Dodonov A.A.
 */
class SecurityRules
{

    /**
     * Method prepares file system for saving file
     *
     * @param string $FilePrefix
     *            Prefix to file path
     * @return string File path
     */
    public function _prepareFs(string $FilePrefix): string
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
    public function storeFileContent(string $FileContent, string $PathPrefix, bool $Decoded = false): string
    {
        $Dir = $this->_prepareFs($PathPrefix);

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
    public function storeFile(string $FilePath, string $PathPrefix, bool $Decoded = false): ?string
    {
        $FileContent = @file_get_contents($FilePath);

        if ($FileContent === false) {
            return (null);
        }

        return ($this->storeFileContent($FileContent, $PathPrefix, $Decoded));
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
    public function getFileValue($Value, bool $StoreFiles)
    {
        if (is_string($Value)) {
            $Value = $_FILES[$Value];
        }

        if (isset($Value['size']) && $Value['size'] === 0) {
            return ('');
        }

        if ($StoreFiles) {
            $Dir = '.' . $this->_prepareFs('.');

            $UploadFile = $Dir . md5($Value['name'] . microtime(true)) . '.' .
                pathinfo($Value['name'], PATHINFO_EXTENSION);

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
     * Returning string value
     *
     * @param string $Value
     *            Value to be made secure
     * @return string Secure value
     */
    public function getStringValue(string $Value): string
    {
        if ($Value == '""') {
            return ('');
        } else {
            return (htmlspecialchars($Value));
        }
    }
}