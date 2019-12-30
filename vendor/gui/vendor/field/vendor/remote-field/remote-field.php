<?php
namespace Mezon\Gui\Field;

/**
 * Class RemoteField
 *
 * @package Field
 * @subpackage RemoteField
 * @author Dodonov A.A.
 * @version v.1.0 (2019/09/13)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Remote field control
 */
class RemoteField extends \Mezon\Gui\Field
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
     * @return \Mezon\CrudService\CrudServiceClient Service client
     */
    protected function getClient(): \Mezon\CrudService\CrudServiceClient
    {
        // @codeCoverageIgnoreStart
        $ExternalRecords = new \Mezon\CrudService\CrudServiceClient($this->RemoteSource);
        $ExternalRecords->setToken($this->SessionId);
        return ($ExternalRecords);
        // @codeCoverageIgnoreEnd
    }
}

?>