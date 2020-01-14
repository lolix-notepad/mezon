<?php
namespace Mezon\CustomClient\CurlWrapper;

/**
 * Class CurlWrapper
 *
 * @package CustomClient
 * @subpackage CurlWrapper
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/07)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Wrapper for CURL routines
 */
class CurlWrapper
{

    /**
     * Method send HTTP request
     *
     * @param string $URL
     *            URL
     * @param array $Headers
     *            Headers
     * @param string $Method
     *            Request HTTP Method
     * @param array $Data
     *            Request data
     * @return array Response body and HTTP code
     */
    public static function sendRequest(string $URL, array $Headers, string $Method, array $Data = []): array
    {
        $Ch = curl_init();

        $CurlConfig = [
            CURLOPT_URL => $URL,
            CURLOPT_HTTPHEADER => $Headers,
            CURLOPT_POST => ($Method == 'POST'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true
        ];

        if ($Method == 'POST') {
            $FormData = [];
            foreach ($Data as $Key => $Value) {
                $FormData[] = $Key . '=' . urldecode($Value);
            }
            $CurlConfig[CURLOPT_POSTFIELDS] = implode('&', $FormData);
        }

        curl_setopt_array($Ch, $CurlConfig);

        $Body = curl_exec($Ch);
        $Code = curl_getinfo($Ch, CURLINFO_HTTP_CODE);

        curl_close($Ch);

        return ([
            $Body,
            $Code
        ]);
    }
}
