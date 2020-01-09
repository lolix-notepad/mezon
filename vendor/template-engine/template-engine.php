<?php
namespace Mezon;

/**
 * Class TemplateEngine
 *
 * @package Mezon
 * @subpackage TemplateEngine
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/17)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Template engine class.
 */
class TemplateEngine
{

    /**
     * Method returns starts and ends of the block
     *
     * @param array $Positions
     *            Starting and ending positions of the blocks
     * @return array Updated positions
     */
    protected static function getPossibleBlockPositions(array &$Positions): array
    {
        $StartPos = $EndPos = false;
        $c = 0;

        foreach ($Positions as $Key => $Value) {
            if ($StartPos === false && $Value === 's') {
                $c ++;
                $StartPos = $Key;
            } elseif ($EndPos === false && $Value === 'e' && $c === 1) {
                $EndPos = $Key;
                break;
            } elseif ($Value === 's' || $Value === 'e' && $c > 0) {
                $c += $Value === 's' ? 1 : - 1;
            }
        }

        return ([
            $StartPos,
            $EndPos
        ]);
    }

    /**
     * Method returns block's start and end
     *
     * @param string $String
     *            Parsing string
     * @param string $BlockStart
     *            Block start
     * @param string $BlockEnd
     *            Block end
     * @return array Starting and ending positions of the block
     */
    protected static function getAllBlockPositions(string $String, string $BlockStart, string $BlockEnd): array
    {
        $Positions = [];
        $StartPos = strpos($String, '{' . $BlockStart . '}', 0);
        $EndPos = - 1;

        if ($StartPos !== false) {
            $Positions[$StartPos] = 's';
            $BlockStart = explode(':', $BlockStart);
            $BlockStart = $BlockStart[0];
            while (($StartPos = strpos($String, '{' . $BlockStart . ':', $StartPos + 1)) !== false) {
                $Positions[$StartPos] = 's';
            }
        }
        while ($EndPos = strpos($String, '{' . $BlockEnd . '}', $EndPos + 1)) {
            $Positions[$EndPos] = 'e';
        }
        ksort($Positions);

        return ($Positions);
    }

    /**
     * Method returns block's start and end
     *
     * @param string $String
     *            Parsing string
     * @param string $BlockStart
     *            Block start
     * @param string $BlockEnd
     *            Block end
     * @return array Positions of the beginning and the end
     */
    protected static function getBlockPositions(string $String, string $BlockStart, string $BlockEnd): array
    {
        $Positions = self::getAllBlockPositions($String, $BlockStart, $BlockEnd);

        list ($StartPos, $EndPos) = self::getPossibleBlockPositions($Positions);

        if ($StartPos === false) {
            return ([
                false,
                false
            ]);
        }
        if ($EndPos === false) {
            throw (new \Exception('Block end was not found'));
        }

        return ([
            $StartPos,
            $EndPos
        ]);
    }

    /**
     * Method returns content between {$BlockStart} and {$BlockEnd} tags
     *
     * @param string $String
     *            processing string
     * @param string $BlockStart
     *            start of the block
     * @param string $BlockEnd
     *            end of the block
     * @return mixed Block content. Or false if the block was not found
     */
    public static function getBlockData(string $String, string $BlockStart, string $BlockEnd)
    {
        list ($StartPos, $EndPos) = self::getBlockPositions($String, $BlockStart, $BlockEnd);

        if ($StartPos !== false) {
            $BlockData = substr(
                $String,
                $StartPos + strlen('{' . $BlockStart . '}'),
                $EndPos - $StartPos - strlen('{' . $BlockStart . '}'));

            return ($BlockData);
        } else {
            return (false);
        }
    }

    /**
     * Getting macro start
     *
     * @param integer $TmpStartPos
     *            Search temporary starting position
     * @param integer $TmpEndPos
     *            Search temporary ending position
     * @param integer $StartPos
     *            Search starting position
     * @param integer $Counter
     *            Brackets counter
     */
    protected static function handleMacroStart(int $TmpStartPos, int $TmpEndPos, int &$StartPos, int &$Counter)
    {
        if ($TmpStartPos !== false && $TmpEndPos !== false) {
            if ($TmpStartPos < $TmpEndPos) {
                $StartPos = $TmpEndPos;
            }
            if ($TmpEndPos < $TmpStartPos) {
                $Counter --;
                if ($Counter) {
                    $Counter ++;
                }
                $StartPos = $TmpStartPos;
            }
        }
    }

    /**
     * Getting macro end
     *
     * @param integer $TmpStartPos
     *            Search temporary starting position
     * @param integer $TmpEndPos
     *            Search temporary ending position
     * @param integer $StartPos
     *            Search starting position
     * @param integer $Counter
     *            Brackets counter
     * @param integer $MacroStartPos
     *            Position of the macro
     */
    protected static function handleMacroEnd(
        int $TmpStartPos,
        int $TmpEndPos,
        int &$StartPos,
        int &$Counter,
        int $MacroStartPos)
    {
        if ($TmpStartPos !== false && $TmpEndPos === false) {
            $Counter ++;
            $StartPos = $TmpStartPos;
        }

        if ($TmpStartPos === false && $TmpEndPos !== false) {
            $Counter --;
            $StartPos = $TmpEndPos;
        }

        if ($TmpStartPos === false && $TmpEndPos === false) {
            /* nothing was found, so $StartPos will be set with the length of $StringData */
            $StartPos = $MacroStartPos;
        }
    }

    /**
     * Getting macro bounds
     *
     * @param string $StringData
     *            Parsing string
     * @param integer $TmpStartPos
     *            Search temporary starting position
     * @param integer $TmpEndPos
     *            Search temporary ending position
     * @param integer $StartPos
     *            Search starting position
     * @param integer $Counter
     *            Brackets counter
     * @param integer $MacroStartPos
     *            Position of the macro
     */
    protected static function handleMacroStartEnd(
        &$StringData,
        &$TmpStartPos,
        &$TmpEndPos,
        &$StartPos,
        &$Counter,
        $MacroStartPos)
    {
        $TmpStartPos = strpos($StringData, '{', $StartPos + 1);
        $TmpEndPos = strpos($StringData, '}', $StartPos + 1);

        self::handleMacroStart($TmpStartPos, $TmpEndPos, $StartPos, $Counter);

        self::handleMacroEnd($TmpStartPos, $TmpEndPos, $StartPos, $Counter, $MacroStartPos);
    }

    /**
     * Getting macro start
     *
     * @param string $StringData
     *            Parsing string
     * @param integer $TmpStartPos
     *            Search temporary starting position
     * @param integer $TmpEndPos
     *            Search temporary ending position
     * @param integer $StartPos
     *            Search starting position
     * @param integer $Counter
     *            Brackets counter
     * @param integer $MacroStartPos
     *            Position of the macro
     * @param integer $ParamStartPos
     *            Position of macro's parameters
     * @return string Macro parameters or false otherwise
     */
    public static function findMacro(
        &$StringData,
        &$TmpStartPos,
        &$TmpEndPos,
        &$StartPos,
        &$Counter,
        $MacroStartPos,
        $ParamStartPos)
    {
        do {
            self::handleMacroStartEnd($StringData, $TmpStartPos, $TmpEndPos, $StartPos, $Counter, $MacroStartPos);

            if ($Counter == 0) {
                return (substr($StringData, $ParamStartPos, $TmpEndPos - $ParamStartPos));
            }
        } while ($TmpStartPos);

        return (false);
    }

    /**
     * Method fetches macro parameters
     *
     * @param string $String
     *            string to be parsed
     * @param string $Name
     *            macro name
     * @param integer $StartPos
     *            starting position of the search
     * @return mixed Macro parameters or false if the macro was not found
     */
    public static function getMacrParameters($String, $Name, $StartPos = - 1)
    {
        while (($TmpStartPos = strpos($String, '{' . $Name . ':', $StartPos + 1)) !== false) {
            $Counter = 1;
            $StartPos = $TmpEndPos = $TmpStartPos;

            $MacroStartPos = $StartPos;
            $ParamStartPos = $MacroStartPos + strlen('{' . $Name . ':');

            $Result = self::findMacro(
                $String,
                $TmpStartPos,
                $TmpEndPos,
                $StartPos,
                $Counter,
                $MacroStartPos,
                $ParamStartPos);

            if ($Result !== false) {
                return ($Result);
            }
        }

        return (false);
    }

    /**
     * Method applyes data for foreach block content
     *
     * @param string $Str
     *            string to process
     * @param string $Parameters
     *            block parameters
     * @param mixed $Data
     *            replacement data
     * @param
     *            string Processed string
     */
    protected static function applyForeachData($Str, $Parameters, $Data)
    {
        $SubTemplate = self::getBlockData($Str, "foreach:$Parameters", '~foreach');

        $BlockStart = "{foreach:$Parameters}";

        $RecordCounter = 1;

        foreach ($Data as $v) {
            $SingleRecordTemplate = str_replace('{n}', $RecordCounter ++, $SubTemplate);

            $Str = str_replace($BlockStart, self::printRecord($SingleRecordTemplate, $v) . $BlockStart, $Str);
        }

        return ($Str);
    }

    /**
     * Method applyes data for print block content
     *
     * @param string $Str
     *            string to process
     * @param string $Parameters
     *            block parameters
     * @param mixed $Data
     *            replacement data
     * @param
     *            string Processed string
     */
    protected static function applyPrintData($Str, $Parameters, $Data)
    {
        $SubTemplate = self::getBlockData($Str, "print:$Parameters", '~print');

        $BlockStart = "{print:$Parameters}";

        $Str = str_replace($BlockStart, self::unwrapBlocks($SubTemplate, $Data) . $BlockStart, $Str);

        return ($Str);
    }

    /**
     * Method replaces block with content
     *
     * @param string $Str
     *            string to process
     * @param string $BlockStart
     *            starting marker of the block
     * @param string $BlockEnd
     *            ending marker of the block
     * @param string $Content
     *            content to replace block
     * @param
     *            string Processed string
     */
    public static function replaceBlock($Str, $BlockStart, $BlockEnd, $Content)
    {
        list ($StartPos, $EndPos) = self::getBlockPositions($Str, $BlockStart, $BlockEnd);

        if ($StartPos !== false) {
            $Str = substr_replace(
                $Str,
                $Content,
                $StartPos,
                $EndPos - $StartPos + strlen(chr(123) . $BlockEnd . chr(125)));
        }

        return ($Str);
    }

    /**
     * Method processes 'print' macro
     *
     * @param string $String
     *            processing string
     * @param mixed $Record
     *            printing record
     * @return string Processed string
     */
    public static function compilePrint($String, &$Record): string
    {
        $StartPos = - 1;

        while ($Parameters = self::getMacrParameters($String, 'print', $StartPos)) {
            if (Functional::fieldExists($Record, $Parameters)) {
                $Data = Functional::getField($Record, $Parameters);

                $String = self::applyPrintData($String, $Parameters, $Data);

                $String = self::replaceBlock($String, "print:$Parameters", '~print', '');
            } else {
                $StartPos = strpos($String, "{print:$Parameters", $StartPos > 0 ? $StartPos : 0);
            }
        }

        return ($String);
    }

    /**
     * Method processes 'foreach' macro
     *
     * @param string $String
     *            processing string
     * @param mixed $Record
     *            printing record
     * @return string Processed string
     */
    public static function compileForeach($String, &$Record): string
    {
        $StartPos = - 1;

        while ($Parameters = self::getMacrParameters($String, 'foreach', $StartPos)) {
            if (Functional::fieldExists($Record, $Parameters)) {
                $Data = Functional::getField($Record, $Parameters);

                $String = self::applyForeachData($String, $Parameters, $Data);

                $String = self::replaceBlock($String, "foreach:$Parameters", '~foreach', '');
            } else {
                $StartPos = strpos($String, "{foreach:$Parameters", $StartPos > 0 ? $StartPos : 0);
            }
        }

        return ($String);
    }

    /**
     * Method processes values substitution
     *
     * @param string $String
     *            processing string
     * @param mixed $Record
     *            printing record
     * @return string Processed string
     */
    public static function compileValues($String, $Record): string
    {
        foreach ($Record as $Field => $Value) {
            if (is_array($Value) || is_object($Value)) {
                $String = self::unwrapBlocks($String, $Value);
            } else {
                $String = str_replace('{' . $Field . '}', $Value, $String);
            }
        }

        return ($String);
    }

    /**
     * Method returns true if the params are terminal, false otherwise
     *
     * @param string $Parameters
     *            Parameters to be analized
     * @return bool true if the params are terminal, false otherwise
     */
    protected static function areTerminalParams(string $Parameters): bool
    {
        return (strpos($Parameters, '}') === false && strpos($Parameters, '{') === false);
    }

    /**
     * Method processes 'switch' macro
     *
     * @param string $String
     *            processing string
     * @return string Processed string
     */
    public static function compileSwitch($String): string
    {
        $StartPos = - 1;

        while (($Parameters = self::getMacrParameters($String, 'switch', $StartPos)) !== false) {
            if (self::areTerminalParams($Parameters)) {
                $SwitchBody = self::getBlockData($String, "switch:$Parameters", '~switch');

                $CaseBody = self::getBlockData($SwitchBody, "case:$Parameters", '~case');

                $String = self::replaceBlock($String, "switch:$Parameters", '~switch', $CaseBody);
            } else {
                $StartPos = strpos($String, '{switch:', $StartPos + 8);
            }
        }

        return ($String);
    }

    /**
     * Method unwraps data
     *
     * @param string $String
     *            processing string
     * @param mixed $Record
     *            printing record
     * @param
     *            string Processed string
     */
    public static function unwrapBlocks(string $String, $Record): string
    {
        $String = self::compilePrint($String, $Record);

        $String = self::compileForeach($String, $Record);

        $String = self::compileValues($String, $Record);

        return ($String);
    }

    /**
     * Method replaces all {var-name} placeholders in $String with fields from $Record
     *
     * @param string $String
     *            processing string
     * @param mixed $Record
     *            printing record
     * @param
     *            string Processed string
     */
    public static function printRecord(string $String, $Record): string
    {
        if (is_array($Record) === false && is_object($Record) === false) {
            throw (new \Exception('Invalid record was passed'));
        }

        $String = self::unwrapBlocks($String, $Record);

        $String = self::compileSwitch($String);

        return ($String);
    }
}
