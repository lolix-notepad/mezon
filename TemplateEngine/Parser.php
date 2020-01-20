<?php
namespace Mezon\TemplateEngine;

/**
 * Class Parser
 *
 * @package Mezon
 * @subpackage TemplateEngine
 * @author Dodonov A.A.
 * @version v.1.0 (2020/01/20)
 * @copyright Copyright (c) 2020, aeon.org
 */

/**
 * Parsing algorithms
 */
class Parser{
    /**
     * Method returns starts and ends of the block
     *
     * @param array $positions
     *            Starting and ending positions of the blocks
     * @return array Updated positions
     */
    public static function getPossibleBlockPositions(array &$positions): array
    {
        $startPos = $endPos = false;
        $c = 0;

        foreach ($positions as $key => $value) {
            if ($startPos === false && $value === 's') {
                $c ++;
                $startPos = $key;
            } elseif ($endPos === false && $value === 'e' && $c === 1) {
                $endPos = $key;
                break;
            } elseif ($value === 's' || $value === 'e' && $c > 0) {
                $c += $value === 's' ? 1 : - 1;
            }
        }

        return [
            $startPos,
            $endPos
        ];
    }

    /**
     * Method returns block's start and end
     *
     * @param string $string
     *            Parsing string
     * @param string $blockStart
     *            Block start
     * @param string $blockEnd
     *            Block end
     * @return array Starting and ending positions of the block
     */
    public static function getAllBlockPositions(string $string, string $blockStart, string $blockEnd): array
    {
        $positions = [];
        $startPos = strpos($string, '{' . $blockStart . '}', 0);
        $endPos = - 1;

        if ($startPos !== false) {
            $positions[$startPos] = 's';
            $blockStart = explode(':', $blockStart);
            $blockStart = $blockStart[0];
            while (($startPos = strpos($string, '{' . $blockStart . ':', $startPos + 1)) !== false) {
                $positions[$startPos] = 's';
            }
        }
        while ($endPos = strpos($string, '{' . $blockEnd . '}', $endPos + 1)) {
            $positions[$endPos] = 'e';
        }
        ksort($positions);

        return $positions;
    }

    /**
     * Method returns block's start and end
     *
     * @param string $string
     *            Parsing string
     * @param string $blockStart
     *            Block start
     * @param string $blockEnd
     *            Block end
     * @return array Positions of the beginning and the end
     */
    public static function getBlockPositions(string $string, string $blockStart, string $blockEnd): array
    {
        $positions = self::getAllBlockPositions($string, $blockStart, $blockEnd);

        list ($startPos, $endPos) = self::getPossibleBlockPositions($positions);

        if ($startPos === false) {
            return [
                false,
                false
            ];
        }
        if ($endPos === false) {
            throw (new \Exception('Block end was not found'));
        }

        return [
            $startPos,
            $endPos
        ];
    }
}
