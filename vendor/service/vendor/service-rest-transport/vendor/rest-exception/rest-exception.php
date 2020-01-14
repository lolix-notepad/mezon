<?php
namespace Mezon\Service\ServiceRestTransport\RestException;

/**
 * Class RestException
 *
 * @package Mezon
 * @subpackage RestException
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/15)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Class for rest exceptions
 */
class RestException extends \Exception
{

    /**
     * HTTP response code
     *
     * @var int
     */
    protected $HTTPCode = 0;

    /**
     * HTTP response body
     *
     * @var string
     */
    protected $HTTPBody = '';

    /**
     * HTTP response URL
     *
     * @var string
     */
    protected $URL = '';

    /**
     * HTTP request options
     *
     * @var string
     */
    protected $Options = false;

    /**
     * Constructor
     *
     * @param string $Message
     *            Error description
     * @param int $Code
     *            Code of the error
     * @param int $HTTPCode
     *            Response HTTP code
     * @param string $HTTPBody
     *            Body of the response
     * @param string $URL
     *            Request URL
     * @param array $Options
     *            Request options
     */
    public function __construct(
        string $Message,
        int $Code,
        string $HTTPCode,
        string $HTTPBody,
        string $URL = '',
        array $Options = [])
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
     * @return int HTTP code
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
