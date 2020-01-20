<?php
namespace Mezon\Functional;

/**
 * Class Compare
 *
 * @package Mezon
 * @subpackage Functional
 * @author Dodonov A.A.
 * @version v.1.0 (2020/01/20)
 * @copyright Copyright (c) 2020, aeon.org
 */

/**
 * Fetching algorithms
 */
class Fetcher
{

    /**
     * Method returns field of the object/array without recursive inspection
     *
     * @param mixed $record
     *            Record to be analyzed
     * @param string $field
     *            Field name
     * @return bool Does the field $field exists or not
     */
    public static function fieldExistsPlain(&$record, string $field): bool
    {
        if (is_object($record) && isset($record->$field)) {
            return true;
        } elseif (is_array($record) && isset($record[$field])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method returns field of the object/array
     *
     * @param mixed $record
     *            Record to be analyzed
     * @param string $field
     *            Field name
     * @param bool $recursive
     *            Do we need recursive descending
     * @return bool Does the field $field exists or not
     */
    public static function fieldExists(&$record, string $field, bool $recursive = true): bool
    {
        if ($recursive) {
            foreach ($record as $v) {
                if (is_array($v) || is_object($v)) {
                    $result = self::fieldExists($v, $field);

                    if ($result === true) {
                        return $result;
                    }
                }
            }
        }

        return self::fieldExistsPlain($record, $field);
    }
}