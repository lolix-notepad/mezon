<?php
/**
 * Class DateTimeUtils
 *
 * @package     GUI
 * @subpackage  DateTimeUtils
 * @author      Dodonov A.A.
 * @version     v.1.0 (2019/09/18)
 * @copyright   Copyright (c) 2019, aeon.org
 */
define('DEFAULT_DATE_MASK', 'Y-m-d');

/**
 * Class provides dati-time routines
 */
class DateTimeUtils
{

    /**
     * Method returns true if the $Date is today
     *
     * @param string $Date
     *            Date to be analyzed
     * @return bool true if the $Date is today, false other wise
     */
    public static function is_today(string $Date): bool
    {
        return (date(DEFAULT_DATE_MASK) == date(DEFAULT_DATE_MASK, strtotime($Date)));
    }

    /**
     * Method returns true if the $Date is yesterday
     *
     * @param string $Date
     *            Date to be analyzed
     * @return bool true if the $Date is yesterday, false other wise
     */
    public static function is_yesterday(string $Date): bool
    {
        return (date(DEFAULT_DATE_MASK, strtotime('-1 day')) == date(DEFAULT_DATE_MASK, strtotime($Date)));
    }

    /**
     * Locale setting
     *
     * @var string
     */
    public static $Locale = 'ru';

    /**
     * Getting localized dictionary
     * 
     * @return array Dictionary
     */
    protected static function get_dictionary(): array
    {
        return (json_decode(file_get_contents(__DIR__ . '/res/l8n/' . self::$Locale . '.json'), true));
    }

    /**
     * Method converts date to 'day full month name'
     *
     * @param string $Date
     *            Date to be converted
     * @return string Converted date
     */
    public static function day_month(string $Date): string
    {
        $Dictionary = self::get_dictionary();

        $DateTime = strtotime($Date);

        return (date('d', $DateTime) . ' ' . $Dictionary[date('n', $DateTime)]);
    }
}

?>