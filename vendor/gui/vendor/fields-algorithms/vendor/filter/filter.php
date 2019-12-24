<?php
namespace Mezon\GUI\FieldsAlgorithms;
/**
 * Class Filter
 *
 * @package     FieldsAlgorithms
 * @subpackage  Filter
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/09/15)
 * @copyright   Copyright (c) 2019, aeon.org
 */

/**
 * Class for compiling filter statement
 */
class Filter
{

    /**
     * Method returns simple operator
     *
     * @param array $Item
     *            Expression item with operator
     * @return string Operator.
     */
    protected static function getOperator(array $Item): string
    {
        $Item['op'] = strtolower($Item['op']);

        $Operators = array(
            '<',
            '>',
            '<=',
            '>=',
            '=',
            'like',
            '!=',
            'and',
            'or',
            'in'
        );

        if (in_array($Item['op'], $Operators)) {
            return ($Item['op']);
        }

        throw (new \Exception('Invalid operator ' . $Item['op']));
    }

    /**
     * Method returns argument
     *
     * @param string $Arg
     *            Argument name orr value
     * @param mixed $Op
     *            Operator
     * @return string Argument
     */
    protected static function getArg($Arg, $Op = false): string
    {
        if (is_array($Arg) && $Op === 'in') {
            $Result = '( ' . implode(' , ', $Arg) . ' )';
        } elseif (is_array($Arg)) {
            $Result = '( ' . self::getStatement($Arg) . ' )';
        } elseif (strpos($Arg, '$') === 0) {
            $Result = substr($Arg, 1);
        } else {
            if (is_numeric($Arg)) {
                $Result = $Arg;
            } else {
                $Result = "'" . $Arg . "'";
            }
        }
        return ($Result);
    }

    /**
     * Method compiles statement
     *
     * @param array $Item
     *            Expression
     * @return string Compiled expression
     */
    protected static function getStatement(array $Item): string
    {
        $Statement = self::getArg($Item['arg1']);

        $Statement .= ' ' . self::getOperator($Item) . ' ';

        $Statement .= self::getArg($Item['arg2'], self::getOperator($Item));

        return ($Statement);
    }

    /**
     * Complex where compilation
     *
     * @param array $Arr
     *            List of structured expressions
     * @param array $Where
     *            List of compiled conditions
     * @return array New list of compiled conditons
     */
    protected static function compileWhere(array $Arr, array $Where): array
    {
        foreach ($Arr as $Item) {
            $Where[] = self::getStatement($Item);
        }

        return ($Where);
    }

    /**
     * Method adds where condition
     *
     * @param array $Arr
     *            Array of fields to be fetched
     * @param array $Where
     *            Conditions
     * @return array Conditions
     */
    public static function addFilterConditionFromArr(array $Arr, array $Where): array
    {
        $FirstElement = array_slice($Arr, - 1);
        $FirstElement = array_pop($FirstElement);

        if (count($Arr) && is_array($FirstElement)) {
            return (self::compileWhere($Arr, $Where));
        }

        // simple filter construction
        foreach ($Arr as $Field => $Value) {
            if (is_numeric($Value)) {
                $Where[] = htmlspecialchars($Field) . ' = ' . $Value;
            } elseif ($Value == 'null') {
                $Where[] = htmlspecialchars($Field) . ' IS NULL';
            } elseif ($Value == 'not null') {
                $Where[] = htmlspecialchars($Field) . ' IS NOT NULL';
            } else {
                $Where[] = htmlspecialchars($Field) . ' LIKE "' . htmlspecialchars($Value) . '"';
            }
        }

        return ($Where);
    }

    /**
     * Method adds where condition
     *
     * @param array $Where
     *            Conditions
     * @return array Conditions
     */
    public static function addFilterCondition($Where)
    {
        if (! isset($_GET['filter'])) {
            return ($Where);
        }

        return (self::addFilterConditionFromArr($_GET['filter'], $Where));
    }
}

?>