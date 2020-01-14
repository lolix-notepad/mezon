<?php
namespace Mezon;

/**
 * Class Functional
 *
 * @package Mezon
 * @subpackage Functional
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/07)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Functional algorithms.
 */
class Functional
{

    /**
     * Method returns field of the object/array without recursinve inspection
     *
     * @param mixed $Record
     *            Processing record
     * @param string $Field
     *            Field name
     * @return mixed Field value
     * @deprecated
     */
    public static function getFieldPlain($Record, string $Field)
    {
        if (is_array($Record) && isset($Record[$Field])) {
            return ($Record[$Field]);
        } elseif (is_object($Record) && isset($Record->$Field)) {
            return ($Record->$Field);
        } else {
            return (null);
        }
    }

    /**
     * Method returns field of the object/array
     *
     * @param mixed $Record
     *            Processing record
     * @param string $Field
     *            Field name
     * @param bool $Recursive
     *            Shold we search the field $Field along the whole object
     * @return mixed Field value
     * @deprecated
     */
    public static function getField($Record, string $Field, bool $Recursive = true)
    {
        if ($Recursive) {
            foreach ($Record as $v) {
                if (is_array($v) || is_object($v)) {
                    $Result = self::getField($v, $Field);

                    if ($Result !== null) {
                        return ($Result);
                    }
                }
            }
        }

        return (self::getFieldPlain($Record, $Field));
    }

    /**
     * Method sets existing field of the object/array
     *
     * @param mixed $Record
     *            Processing record
     * @param string $Field
     *            Field name
     * @param mixed $Value
     *            Value to be set
     */
    protected static function setExistingField(&$Record, string $Field, $Value)
    {
        foreach ($Record as $i => $v) {
            if (is_array($v) || is_object($v)) {
                self::setExistingField($v, $Field, $Value);
            } elseif ($i == $Field) {
                if (is_object($Record)) {
                    $Record->$Field = $Value;
                } else {
                    $Record[$Field] = $Value;
                }
            }
        }
    }

    /**
     * Method sets field of the object/array
     *
     * @param mixed $Record
     *            Processing record
     * @param string $Field
     *            Field name
     * @param mixed $Value
     *            Value to be set
     */
    public static function setField(&$Record, string $Field, $Value)
    {
        $Existing = self::getField($Record, $Field);

        if ($Existing == null) {
            // add field if it does not exist
            if (is_object($Record)) {
                $Record->$Field = $Value;
            } else {
                $Record[$Field] = $Value;
            }
        } else {
            // set existing field
            self::setExistingField($Record, $Field, $Value);
        }
    }

    /**
     * Method fetches all fields from objects/arrays of an array
     *
     * @param mixed $Data
     *            Processing record
     * @param string $Field
     *            Field name
     * @param bool $Recursive
     *            Shold we search the field $Field along the whole object
     */
    public static function getFields($Data, string $Field, $Recursive = true)
    {
        $Return = array();

        foreach ($Data as $Record) {
            $Return[] = self::getField($Record, $Field, $Recursive);
        }

        return ($Return);
    }

    /**
     * Method sets fields $FieldName in array of objects $Objects with $Values
     *
     * @param array $Objects
     *            Array of objects to be processed
     * @param string $FieldName
     *            Field name
     * @param array $Values
     *            Values to be set
     */
    public static function setFieldsInObjects(&$Objects, string $FieldName, array $Values)
    {
        foreach ($Values as $i => $Value) {
            if (isset($Objects[$i]) === false) {
                $Objects[$i] = new \stdClass();
            }

            $Objects[$i]->$FieldName = $Value;
        }
    }

    /**
     * Method sums fields in an array of objects.
     *
     * @param array $Objects
     *            Array of objects to be processed
     * @param string $FieldName
     *            Field name
     * @return mixed Sum of fields.
     */
    public static function sumFields(&$Objects, $FieldName)
    {
        $Sum = 0;

        foreach ($Objects as $Object) {
            if (is_array($Object)) {
                $Sum += self::sumFields($Object, $FieldName);
            } else {
                $Sum += $Object->$FieldName;
            }
        }

        return ($Sum);
    }

    /**
     * Method transforms objects in array
     *
     * @param array $Objects
     *            Array of objects to be processed
     * @param callback $Transformer
     *            Transform function
     */
    public static function transform(&$Objects, $Transformer)
    {
        foreach ($Objects as $i => $Object) {
            $Objects[$i] = call_user_func($Transformer, $Object);
        }
    }

    /**
     * Method filters objects in array
     *
     * @param array $Objects
     *            List of records to be filtered
     * @param string $Field
     *            Filter field
     * @param string $Operation
     *            Filtration operation
     * @param mixed $Value
     *            Filtration value
     * @param bool $Recursive
     *            Recursive mode
     * @return array List of filtered records
     */
    public static function filter(
        array &$Objects,
        string $Field,
        string $Operation = '==',
        $Value = false,
        bool $Recursive = true): array
    {
        $Return = [];

        foreach ($Objects as $Object) {
            // TODO make availabale any operations. not only the specified ones
            if (is_array($Object) && $Recursive === true) {
                $Return = array_merge($Return, self::filter($Object, $Field, $Operation, $Value));
            } elseif ($Operation == '==' && self::getField($Object, $Field) == $Value) {
                $Return[] = $Object;
            } elseif ($Operation == '>' && self::getField($Object, $Field) > $Value) {
                $Return[] = $Object;
            } elseif ($Operation == '<' && self::getField($Object, $Field) < $Value) {
                $Return[] = $Object;
            } elseif ($Operation == '!=' && self::getField($Object, $Field) != $Value) {
                $Return[] = $Object;
            }
        }

        return ($Return);
    }

    /**
     * Method replaces one field to another in record
     *
     * @param array $Object
     *            Object to be processed
     * @param string $FieldFrom
     *            Field name to be replaced
     * @param string $FieldTo
     *            Field name to be added
     */
    public static function replaceFieldInEntity(&$Object, string $FieldFrom, string $FieldTo): void
    {
        if (is_array($Object)) {
            if (isset($Object[$FieldFrom])) {
                $Value = $Object[$FieldFrom];
                unset($Object[$FieldFrom]);
                $Object[$FieldTo] = $Value;
            }
        } elseif (is_object($Object)) {
            if (isset($Object->$FieldFrom)) {
                $Value = $Object->$FieldFrom;
                unset($Object->$FieldFrom);
                $Object->$FieldTo = $Value;
            }
        } else {
            throw (new \Exception('Unknown entyty type for ' . serialize($Object)));
        }
    }

    /**
     * Method replaces one field to another in record
     *
     * @param array $Object
     *            Object to be processed
     * @param array $FieldsFrom
     *            Field names to be replaced
     * @param array $FieldsTo
     *            Field names to be added
     */
    public static function replaceFieldsInEntity(&$Object, array $FieldsFrom, array $FieldsTo): void
    {
        foreach ($FieldsFrom as $i => $FieldFrom) {
            self::replaceFieldInEntity($Object, $FieldFrom, $FieldsTo[$i]);
        }
    }

    /**
     * Method replaces one field to another in array of records
     *
     * @param array $Objects
     *            Objects to be processed
     * @param string $FieldFrom
     *            Field name to be replaced
     * @param string $FieldTo
     *            Field name to be added
     */
    public static function replaceField(array &$Objects, string $FieldFrom, string $FieldTo): void
    {
        foreach ($Objects as $i => $Object) {
            self::replaceFieldInEntity($Object, $FieldFrom, $FieldTo);

            $Objects[$i] = $Object;
        }
    }

    /**
     * Method replaces one field toanother in array of records
     *
     * @param array $Objects
     *            Objects to be processed
     * @param array $FieldsFrom
     *            Field names to be replaced
     * @param array $FieldsTo
     *            Field names to be added
     */
    public static function replaceFields(array &$Objects, array $FieldsFrom, array $FieldsTo): void
    {
        foreach ($FieldsFrom as $i => $FieldFrom) {
            self::replaceField($Objects, $FieldFrom, $FieldsTo[$i]);
        }
    }

    /**
     * Method adds nested records to the original record of objects
     *
     * @param string $Field
     *            Field name
     * @param array $Objects
     *            The original record of objects
     * @param string $ObjectField
     *            Filtering field
     * @param array $Records
     *            List of nested records
     * @param string $RecordField
     *            Filtering field
     * @return array List of tramsformed records
     */
    public static function setChildren(
        string $Field,
        array &$Objects,
        string $ObjectField,
        array $Records,
        string $RecordField): void
    {
        foreach ($Objects as &$Object) {
            self::setField(
                $Object,
                $Field,
                self::filter($Records, $RecordField, '==', self::getField($Object, $ObjectField, false), false));
        }
    }

    /**
     * Method adds nested record to the original record of objects
     *
     * @param string $Field
     *            Field name
     * @param array $Objects
     *            The original record of objects
     * @param string $ObjectField
     *            Filtering field
     * @param array $Records
     *            List of nested records
     * @param string $RecordField
     *            Filtering field
     * @return array List of tramsformed records
     */
    public static function setChild(
        string $Field,
        array &$Objects,
        string $ObjectField,
        array $Records,
        string $RecordField)
    {
        foreach ($Objects as &$Object) {
            foreach ($Records as $Record) {
                if (self::getField($Object, $ObjectField, false) == self::getField($Record, $RecordField, false)) {
                    self::setField($Object, $Field, $Record);
                }
            }
        }
    }

    /**
     * Method unites corresponding records
     *
     * @param array $Dest
     *            Destination array of records
     * @param string $DestField
     *            Field name
     * @param array $Src
     *            Source array of records
     * @param string $SrcField
     *            Field name
     */
    public static function expandRecordsWith(array &$Dest, string $DestField, array $Src, string $SrcField)
    {
        foreach ($Dest as &$DestRecord) {
            foreach ($Src as $SrcRecord) {
                if (self::getField($DestRecord, $DestField, false) == self::getField($SrcRecord, $SrcField, false)) {
                    foreach ($SrcRecord as $SrcRecordField => $SrcRecordValue) {
                        self::setField($DestRecord, $SrcRecordField, $SrcRecordValue);
                    }

                    break;
                }
            }
        }
    }

    /**
     * Sorting directions
     */
    public const SORT_DIRECTION_ASC = 0;

    public const SORT_DIRECTION_DESC = 1;

    /**
     * Method sorts records by the specified field
     *
     * @param array $Objects
     *            Records to be sorted
     * @param string $Field
     *            Field name
     */
    public static function sortRecords(array &$Objects, string $Field, int $Direction = Functional::SORT_DIRECTION_ASC)
    {
        usort(
            $Objects,
            function ($e1, $e2) use ($Field, $Direction) {
                $Value1 = self::getField($e1, $Field, false);
                $Value2 = self::getField($e2, $Field, false);

                $Result = 0;

                if ($Value1 < $Value2) {
                    $Result = - 1;
                } elseif ($Value1 == $Value2) {
                    $Result = 0;
                } else {
                    $Result = 1;
                }

                return ($Direction === Functional::SORT_DIRECTION_ASC ? $Result : - 1 * $Result);
            });
    }

    /**
     * Method returns field of the object/array without recursive inspection
     *
     * @param mixed $Record
     *            Record to be analyzed
     * @param string $Field
     *            Field name
     * @return bool Does the field $Field exists or not
     */
    public static function fieldExistsPlain(&$Record, string $Field): bool
    {
        if (is_object($Record) && isset($Record->$Field)) {
            return (true);
        } elseif (is_array($Record) && isset($Record[$Field])) {
            return (true);
        } else {
            return (false);
        }
    }

    /**
     * Method returns field of the object/array
     *
     * @param mixed $Record
     *            Record to be analyzed
     * @param string $Field
     *            Field name
     * @param bool $Recursive
     *            Do we need recursive descending
     * @return bool Does the field $Field exists or not
     */
    public static function fieldExists(&$Record, string $Field, bool $Recursive = true): bool
    {
        if ($Recursive) {
            foreach ($Record as $v) {
                if (is_array($v) || is_object($v)) {
                    $Result = self::fieldExists($v, $Field);

                    if ($Result === true) {
                        return ($Result);
                    }
                }
            }
        }

        return (self::fieldExistsPlain($Record, $Field));
    }
}
