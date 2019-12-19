<?php
namespace Mezon\Service\ServiceRESTTransport;

/**
 * Class RESTException
 *
 * @package Mezon
 * @subpackage RESTException
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/15)
 * @copyright Copyright (c) 2019, aeon.org
 */

// TODO add camel-case
/**
 * Class for rest exceptions
 */
class RESTException extends \Exception
{

    /**
     * HTTP response code
     *
     * @var integer
     */
    var $HTTPCode = 0;

    /**
     * HTTP response body
     *
     * @var string
     */
    var $HTTPBody = '';

    /**
     * HTTP response URL
     *
     * @var string
     */
    var $URL = '';

    /**
     * HTTP request options
     *
     * @var string
     */
    var $Options = false;

    /**
     * Constructor
     *
     * @param string $Message
     *            Error description
     * @param integer $Code
     *            Code of the error
     * @param integer $HTTPCode
     *            Response HTTP code
     * @param string $HTTPBody
     *            Body of the response
     * @param string $URL
     *            Request URL
     * @param array $Options
     *            Request options
     */
    public function __construct(string $Message, int $Code, string $HTTPCode, string $HTTPBody, string $URL = '', array $Options = [])
    {
        parent::__construct($Message, $Code);

        $this->http_code = $HTTPCode;

        $this->http_body = $HTTPBody;

        $this->url = $URL;

        $this->options = $Options;
    }

    /**
     * Method returns HTTP code
     *
     * @return integer HTTP code
     */
    public function getHttpCode(): int
    {
        return ($this->http_code);
    }

    /**
     * Method returns HTTP body
     *
     * @return string HTTP body
     */
    public function getHttpBody(): string
    {
        return ($this->http_body);
    }

    /**
     * Method returns URL
     *
     * @return string URL
     */
    public function getUrl(): string
    {
        return ($this->url);
    }

    /**
     * Method returns request options
     *
     * @return array Request options
     */
    public function getOptions(): array
    {
        return ($this->options);
    }
}

?>