<?php
namespace Mezon\GUI\Field;

/**
 * Class RemoteField
 *
 * @package Field
 * @subpackage RemoteField
 * @author Dodonov A.A.
 * @version v.1.0 (2019/09/13)
 * @copyright Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../../../../../crud-service/vendor/crud-service-client/crud-service-client.php');

require_once (__DIR__ . '/../../field.php');

/**
 * Remote field control
 */
class RemoteField extends \Mezon\GUI\Field
{

    /**
     * Session id
     *
     * @var string
     */
    var $SessionId = '';

    /**
     * Remote source of records
     *
     * @var string
     */
    var $RemoteSource = '';

    /**
     * Method fetches session id from the description
     *
     * @param array $FieldDescription
     *            Field description
     */
    protected function initSessionId(array $FieldDescription)
    {
        if (isset($FieldDescription['session-id'])) {
            $this->SessionId = $FieldDescription['session-id'];
        } else {
            throw (new \Exception('Session id is not defined', - 1));
        }
    }

    /**
     * Method fetches remote source from the description
     *
     * @param array $FieldDescription
     *            Field description
     */
    protected function initRemoteSource(array $FieldDescription)
    {
        if (isset($FieldDescription['remote-source'])) {
            $this->RemoteSource = $FieldDescription['remote-source'];
        } else {
            throw (new \Exception('Remote source of records is not defined', - 1));
        }
    }

    /**
     * Constructor
     *
     * @param array $FieldDescription
     *            Field description
     * @param string $Value
     *            Field value
     */
    public function __construct(array $FieldDescription, string $Value = '')
    {
        parent::__construct($FieldDescription, $Value);

        $this->initSessionId($FieldDescription);

        $this->initRemoteSource($FieldDescription);
    }

    /**
     * Getting service client
     *
     * @return \Mezon\CRUDService\CRUDServiceClient Service client
     */
    protected function getClient(): \Mezon\CRUDService\CRUDServiceClient
    {
        // @codeCoverageIgnoreStart
        $ExternalRecords = new \Mezon\CRUDService\CRUDServiceClient($this->RemoteSource);
        $ExternalRecords->setToken($this->SessionId);
        return ($ExternalRecords);
        // @codeCoverageIgnoreEnd
    }
}

?>