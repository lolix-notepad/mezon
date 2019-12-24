<?php
namespace Mezon\Service;

/**
 * Class DBServiceModel
 *
 * @package Service
 * @subpackage DBServiceModel
 * @author Dodonov A.A.
 * @version v.1.0 (2019/10/18)
 * @copyright Copyright (c) 2019, aeon.org
 */
require_once (__DIR__ . '/../../../functional/functional.php');
require_once (__DIR__ . '/../../../gui/vendor/fields-algorithms/fields-algorithms.php');
require_once (__DIR__ . '/../../../mezon/mezon.php');
require_once (__DIR__ . '/../../../service/vendor/service-model/service-model.php');

/**
 * CRUD service's default model
 *
 * @author Dodonov A.A.
 */
class DBServiceModel extends ServiceModel
{

    /**
     * Table name
     */
    protected $TableName = '';

    /**
     * Fields algorithms
     */
    protected $FieldsAlgorithms = false;

    /**
     * Entity name
     */
    protected $EntityName = false;

    /**
     * Constructor
     *
     * @param string|array $Fields
     *            fields of the model
     * @param string $TableName
     *            name of the table
     * @param string $EntityName
     *            name of the entity
     */
    public function __construct($Fields = '*', string $TableName = '', string $EntityName = '')
    {
        $this->setTableName($TableName);

        $this->EntityName = $EntityName;

        if (is_string($Fields)) {
            $this->FieldsAlgorithms = new \Mezon\GUI\FieldsAlgorithms([
                '*' => [
                    'type' => 'string',
                    'title' => 'All fields'
                ]
            ], $TableName);
        } elseif (is_array($Fields)) {
            $this->FieldsAlgorithms = new \Mezon\GUI\FieldsAlgorithms($Fields, $TableName);
        } elseif ($Fields instanceof \Mezon\GUI\FieldsAlgorithms) {
            $this->FieldsAlgorithms = $Fields;
        } else {
            throw (new \Exception('Invalid fields description', - 1));
        }
    }

    /**
     * Method sets table name
     *
     * @param string $TableName
     *            Table name
     */
    protected function setTableName(string $TableName = '')
    {
        if (strpos($TableName, '-') !== false && strpos($TableName, '`') === false) {
            $TableName = "`$TableName`";
        }
        $this->TableName = $TableName;
    }

    /**
     * Method returns connection to the DB
     *
     * @return \Mezon\PdoCrud - PDO DB connection
     */
    protected function getConnection(): \Mezon\PdoCrud
    {
        // @codeCoverageIgnoreStart
        return (\Mezon\Mezon::getDbConnection());
        // @codeCoverageIgnoreEnd
    }

    /**
     * Method returns list of all fields as string
     *
     * @return string list of all fields as string
     */
    public function getFieldsNames(): string
    {
        return (implode(', ', $this->FieldsAlgorithms->getFieldsNames()));
    }

    /**
     * Method returns true if the field exists
     *
     * @param string $FieldName
     *            Field name
     * @return bool
     */
    public function hasField(string $FieldName): bool
    {
        // @codeCoverageIgnoreStart
        return ($this->FieldsAlgorithms->hasField($FieldName));
        // @codeCoverageIgnoreEnd
    }

    /**
     * Method returns true if the custom field exists
     *
     * @return bool
     */
    public function hasCustomFields(): bool
    {
        // @codeCoverageIgnoreStart
        return ($this->FieldsAlgorithms->hasCustomFields());
        // @codeCoverageIgnoreEnd
    }

    /**
     * Method validates if the field $Field exists
     *
     * @param string $Field
     *            Field name
     */
    public function validateFieldExistance(string $Field)
    {
        // @codeCoverageIgnoreStart
        return ($this->FieldsAlgorithms->validateFieldExistance($Field));
        // @codeCoverageIgnoreEnd
    }

    /**
     * Method returns fields list
     *
     * @return array Fields list
     */
    public function getFields(): array
    {
        // @codeCoverageIgnoreStart
        return ($this->FieldsAlgorithms->get());
        // @codeCoverageIgnoreEnd
    }

    /**
     * Method returns entity name
     *
     * @return string Entity name
     */
    public function getEntityName(): string
    {
        // @codeCoverageIgnoreStart
        return ($this->EntityName);
        // @codeCoverageIgnoreEnd
    }
}

?>