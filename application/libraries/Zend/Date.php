<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Date
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Date.php 2883 2007-01-18 05:56:31Z gavin $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Include needed Date classes
 */
require_once 'Zend/Date/DateObject.php';
require_once 'Zend/Date/Exception.php';
require_once 'Zend/Locale.php';
require_once 'Zend/Locale/Math.php';


/**
 * @category   Zend
 * @package    Zend_Date
 * @copyright  Copyright (c) 2006-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Date extends Zend_Date_DateObject {

    private $_Locale  = null;
    private $_GMT     = -1;
    private $_GMTRule = false;
    private $_DST     = -1;

    // Fractional second variables 
    private $_Fractional = 0;
    private $_Precision  = 3;

    // Class wide Date Constants
    // day formats
    const DAY            = 'DAY';            // d - 2 digit day of month, 01-31
    const WEEKDAY_SHORT  = 'WEEKDAY_SHORT';  // D - 3 letter day of week - locale aware, Mon-Sun
    const DAY_SHORT      = 'DAY_SHORT';      // j - 1,2 digit day of month, 1-31
    const WEEKDAY        = 'WEEKDAY';        // l - full day name - locale aware, Monday - Sunday
    const WEEKDAY_8601   = 'WEEKDAY_8601';   // N - digit weekday ISO 8601, 1-7 1 = monday, 7=sunday
    const DAY_SUFFIX     = 'DAY_SUFFIX';     // S - english suffix day of month, st-th
    const WEEKDAY_DIGIT  = 'WEEKDAY_DIGIT';  // w - weekday, 0-6 0=sunday, 6=saturday
    const DAY_OF_YEAR    = 'DAY_OF_YEAR';    // z - Number of day of year

    const WEEKDAY_NARROW = 'WEEKDAY_NARROW'; // --- 1 letter day name - locale aware, M-S
    const WEEKDAY_NAME   = 'WEEKDAY_NAME';   // --- 2 letter day name - locale aware,Mo-Su

    // week formats
    const WEEK           = 'WEEK';           // W - number of week ISO8601, 1-53

    // month formats
    const MONTH          = 'MONTH';          // F - full month name - locale aware, January-December
    const MONTH_SHORT    = 'MONTH_SHORT';    // m - 2 digit month, 01-12
    const MONTH_NAME     = 'MONTH_NAME';     // M - 3 letter monthname - locale aware, Jan-Dec
    const MONTH_DIGIT    = 'MONTH_DIGIT';    // n - 1 digit month, no leading zeros, 1-12
    const MONTH_DAYS     = 'MONTH_DAYS';     // t - Number of days this month

    const MONTH_NARROW   = 'MONTH_NARROW';   // --- 1 letter month name - locale aware, J-D

    // year formats
    const LEAPYEAR       = 'LEAPYEAR';       // L - is leapyear ?, 0-1
    const YEAR_8601      = 'YEAR_8601';      // o - number of year ISO8601
    const YEAR           = 'YEAR';           // Y - 4 digit year
    const YEAR_SHORT     = 'YEAR_SHORT';     // y - 2 digit year, leading zeros 00-99

    const YEAR_SHORT_8601= 'YEAR_SHORT_8601';// --- 2 digit number of year ISO8601

    // time formats
    const MERIDIEM       = 'MERIDIEM';       // A,a - AM/PM - locale aware, AM/PM
    const SWATCH         = 'SWATCH';         // B - Swatch Internet Time
    const HOUR_SHORT_AM  = 'HOUR_SHORT_AM';  // g - 1 digit hour, no leading zero, 1-12 am/pm
    const HOUR_SHORT     = 'HOUR_SHORT';     // G - 1 digit hour, no leading zero, 0-23
    const HOUR_AM        = 'HOUR_AM';        // h - 2 digit hour, leading zeros, 01-12 am/pm
    const HOUR           = 'HOUR';           // H - 2 digit hour, leading zeros, 00-23
    const MINUTE         = 'MINUTE';         // i - 2 digit minute, leading zeros, 00-59
    const SECOND         = 'SECOND';         // s - 2 digit second, leading zeros, 00-59
    const MILLISECOND    = 'MILLISECOND';    // --- milliseconds

    const MINUTE_SHORT   = 'MINUTE_SHORT';   // --- 1 digit minute, no leading zero, 0-59
    const SECOND_SHORT   = 'SECOND_SHORT';   // --- 1 digit second, no leading zero, 0-59

    // timezone formats
    const TIMEZONE_NAME  = 'TIMEZONE_NAME';  // e - timezone string
    const DAYLIGHT       = 'DAYLIGHT';       // I - is Daylight saving time ?, 0-1
    const GMT_DIFF       = 'GMT_DIFF';       // O - GMT difference, -1200 +1200
    const GMT_DIFF_SEP   = 'GMT_DIFF_SEP';   // P - seperated GMT diff, -12:00 +12:00
    const TIMEZONE       = 'TIMEZONE';       // T - timezone, EST, GMT, MDT
    const TIMEZONE_SECS  = 'TIMEZONE_SECS';  // Z - timezone offset in seconds, -43200 +43200

    // date strings
    const ISO_8601       = 'ISO_8601';       // c - ISO 8601 date string
    const RFC_2822       = 'RFC_2822';       // r - RFC 2822 date string
    const TIMESTAMP      = 'TIMESTAMP';      // U - unix timestamp

    // additional formats
    const ERA            = 'ERA';            // --- short name of era, locale aware,
    const ERA_NAME       = 'ERA_NAME';       // --- full name of era, locale aware,
    const DATES          = 'DATES';          // --- standard date, locale aware
    const DATE_FULL      = 'DATE_FULL';      // --- full date, locale aware
    const DATE_LONG      = 'DATE_LONG';      // --- long date, locale aware
    const DATE_MEDIUM    = 'DATE_MEDIUM';    // --- medium date, locale aware
    const DATE_SHORT     = 'DATE_SHORT';     // --- short date, locale aware
    const TIMES          = 'TIMES';          // --- standard time, locale aware
    const TIME_FULL      = 'TIME_FULL';      // --- full time, locale aware
    const TIME_LONG      = 'TIME_LONG';      // --- long time, locale aware
    const TIME_MEDIUM    = 'TIME_MEDIUM';    // --- medium time, locale aware
    const TIME_SHORT     = 'TIME_SHORT';     // --- short time, locale aware
    const ATOM           = 'ATOM';           // --- DATE_ATOM
    const COOKIE         = 'COOKIE';         // --- DATE_COOKIE
    const RFC_822        = 'RFC_822';        // --- DATE_RFC822
    const RFC_850        = 'RFC_850';        // --- DATE_RFC850
    const RFC_1036       = 'RFC_1036';       // --- DATE_RFC1036
    const RFC_1123       = 'RFC_1123';       // --- DATE_RFC1123
    const RFC_3339       = 'RFC_3339';       // --- DATE_RFC3339
    const RSS            = 'RSS';            // --- DATE_RSS
    const W3C            = 'W3C';            // --- DATE_W3C


    /**
     * Generates the standard date object, could be a unix timestamp, localized date, 
     * string, integer and so on. Also parts of dates or time are supported
     * Always set the default timezone: http://php.net/date_default_timezone_set
     * For example, in your bootstrap: date_default_timezone_set('America/Los_Angeles');
     * For detailed instructions please look in the docu.
     * 
     * @param  string|integer|Zend_Date  $date    Date value or value of date part to set, depending on $part
     * @param  string                    $part    OPTIONAL Defines the input format of $date
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     * @throws Zend_Date_Exception
     */
    public function __construct($date, $part = null, $locale = null)
    {
        if (is_null($date)) {
            throw new Zend_Date_Exception('parameter $date must be set');
        }

        $type = gettype($part);
        if (($type === 'object' && ($part instanceof Zend_Locale)) or
            ($type === 'string' && (Zend_Locale::isLocale($part)))) {
            $locale = $part;
            $part = null;
        }

        $this->setLocale($locale);

        // set the timezone and offset for $this
        $zone = date_default_timezone_get();
        if ($zone !== 'UTC') {
            $this->setTimezone($zone);
        }

        // set datepart
        if (($part !== null && $part !== Zend_Date::TIMESTAMP) or (!is_numeric($date))) {
            $this->setUnixTimestamp(0);
            $this->set($date, $part, $this->_Locale);
        } else {
            $this->setUnixTimestamp($date);
        }
    }


    /**
     * Returns this object's internal UNIX timestamp (equivalent to Zend_Date::TIMESTAMP).
     * If the timestamp is too large for integers, then the return value will be a string.
     * This function does not return the timestamp as an object.
     * Use clone() or copyPart() instead.
     *
     * @return integer|string  UNIX timestamp
     */
    public function getTimestamp()
    {
        return $this->getUnixTimestamp();
    }


    /**
     * Returns the calculated timestamp
     * HINT: timestamps are always GMT
     *
     * @param  string                    $calc    Type of calculation to make
     * @param  string|integer|Zend_Date  $stamp   Timestamp to calculate, when null the actual timestamp is calculated
     * @param  string|Zend_Locale        $locale  Locale for parsing input
     * @return Zend_Date|integer
     * @throws Zend_Date_Exception
     */
    private function _timestamp($calc, $stamp, $locale)
    {
        if ($locale === null) {
            $locale = $this->getLocale();
        }

        if ($stamp instanceof Zend_Date) {
            // extract timestamp from object
            $stamp = $stamp->get(Zend_Date::TIMESTAMP, true, $locale);
        }

        if ($calc === 'set') {
            $return = $this->setUnixTimestamp($stamp);
        } else {
            $return = $this->_calcdetail($calc, $stamp, Zend_Date::TIMESTAMP, $locale);
        }
        if ($calc != 'cmp') {
            return $this;
        }
        return $return;
    }


    /**
     * Sets a new timestamp
     *
     * @param  integer|string|Zend_Date  $timestamp  Timestamp to set
     * @param  string|Zend_Locale        $locale     OPTIONAL Locale for parsing input
     * @return Zend_Date
     * @throws Zend_Date_Exception
     */
    public function setTimestamp($timestamp, $locale = null)
    {
        return $this->_timestamp('set', $timestamp, $locale);
    }


    /**
     * Adds a timestamp
     *
     * @param  integer|string      $timestamp  Timestamp to add
     * @param  string|Zend_Locale  $locale     OPTIONAL Locale for parsing input
     * @return Zend_Date
     * @throws Zend_Date_Exception
     */
    public function addTimestamp($timestamp, $locale = null)
    {
        return $this->_timestamp('add', $timestamp, $locale);
    }


    /**
     * Subtracts a timestamp
     *
     * @param  integer|string      $timestamp  Timestamp to sub
     * @param  string|Zend_Locale  $locale     OPTIONAL Locale for parsing input
     * @return Zend_Date
     * @throws Zend_Date_Exception
     */
    public function subTimestamp($timestamp, $locale = null)
    {
        return $this->_timestamp('sub', $timestamp, $locale);
    }


    /**
     * Compares two timestamps, returning the difference as integer
     *
     * @param  integer|string      $timestamp  Timestamp to compare
     * @param  string|Zend_Locale  $locale     OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareTimestamp($timestamp, $locale = null)
    {
        return $this->_timestamp('cmp', $timestamp, $locale);
    }


    /**
     * Returns a string representation of the object
     * Supported format tokens are:
     * G - era, y - year, Y - ISO year, M - month, w - week of year, D - day of year, d - day of month
     * E - day of week, e - number of weekday, h - hour 1-12, H - hour 0-23, m - minute, s - second
     * A - milliseconds of day, z - timezone, Z - timezone offset, S - fractional second, a - period of day
     *
     * Not supported tokens are
     * u - extended year, Q - quarter, q - quarter, L - stand alone month, W - week of month
     * F - day of week of month, g - modified julian, c - stand alone weekday, k - hour 0-11, K - hour 1-24
     * v - wall zone
     *
     * @param  string              $format  OPTIONAL An rule for formatting the output, if null the default dateformat is used
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return string
     */
    public function toString($format = null, $locale = null)
    {
        if ($locale === null) {
            $locale = $this->getLocale();
        }

        if (Zend_Locale::isLocale($format)) {
            $locale = $format;
            $format = null;
        }

        if ($format === null) {
            $date = Zend_Locale_Data::getContent($locale, 'defdateformat', 'gregorian');
            $time = Zend_Locale_Data::getContent($locale, 'deftimeformat', 'gregorian');
            $date = Zend_Locale_Data::getContent($locale, 'dateformat', array('gregorian', $date['default']));
            $time = Zend_Locale_Data::getContent($locale, 'timeformat', array('gregorian', $time['default']));

            $format  = $date['pattern'];
            $format .= " ";
            $format .= $time['pattern'];
        }

        // get format tokens
        $j = 0;
        $comment = false;
        $output = array();
        for($i = 0; $i < strlen($format); ++$i) {

            if ($format[$i] == "'") {
                if ($comment == false) {
                    $comment = true;
                    ++$j;
                    $output[$j] = "'";
                } else if (isset($format[$i+1]) and ($format[$i+1] == "'")) {
                    $output[$j] .= "'";
                    ++$i;
                } else {
                    $comment = false;
                }
                continue;
            }

            if (isset($output[$j]) and ($output[$j][0] == $format[$i]) or
                ($comment == true)) {
                $output[$j] .= $format[$i];
            } else {
                ++$j;
                $output[$j] = $format[$i];
            }
        }

        $notset = false;
        // fill format tokens with date information
        for($i = 1; $i <= count($output); ++$i) {
            // fill fixed tokens
            switch ($output[$i]) {

                // eras
                case 'GGGGG' :
                    $output[$i] = substr($this->get(Zend_Date::ERA, $locale), 0, 1) . ".";
                    break;

                case 'GGGG' :
                    $output[$i] = $this->get(Zend_Date::ERA_NAME, $locale);
                    break;

                case 'GGG' :
                case 'GG'  :
                case 'G'   :
                    $output[$i] = $this->get(Zend_Date::ERA, $locale);
                    break;


                // years
                case 'yy' :
                    $output[$i] = $this->get(Zend_Date::YEAR_SHORT, $locale);
                    break;


                // ISO years
                case 'YY' :
                    $output[$i] = $this->get(Zend_Date::YEAR_SHORT_8601, $locale);
                    break;


                // months
                case 'MMMMM' :
                    $output[$i] = substr($this->get(Zend_Date::MONTH_NARROW, $locale), 0, 1);
                    break;

                case 'MMMM' :
                    $output[$i] = $this->get(Zend_Date::MONTH, $locale);
                    break;

                case 'MMM' :
                    $output[$i] = $this->get(Zend_Date::MONTH_NAME, $locale);
                    break;

                case 'MM' :
                    $output[$i] = $this->get(Zend_Date::MONTH_SHORT, $locale);
                    break;

                case 'M' :
                    $output[$i] = $this->get(Zend_Date::MONTH_DIGIT, $locale);
                    break;


                // week
                case 'ww' :
                    $output[$i] = str_pad($this->get(Zend_Date::WEEK, $locale), 2, '0', STR_PAD_LEFT);
                    break;

                case 'w' :
                    $output[$i] = $this->get(Zend_Date::WEEK, $locale);
                    break;


                // monthday
                case 'dd' :
                    $output[$i] = $this->get(Zend_Date::DAY, $locale);
                    break;

                case 'd' :
                    $output[$i] = $this->get(Zend_Date::DAY_SHORT, $locale);
                    break;


                // yearday
                case 'DDD' :
                    $output[$i] = str_pad($this->get(Zend_Date::DAY_OF_YEAR, $locale), 3, '0', STR_PAD_LEFT);
                    break;

                case 'DD' :
                    $output[$i] = str_pad($this->get(Zend_Date::DAY_OF_YEAR, $locale), 2, '0', STR_PAD_LEFT);
                    break;

                case 'D' :
                    $output[$i] = $this->get(Zend_Date::DAY_OF_YEAR, $locale);
                    break;


                // weekday
                case 'EEEEE' :
                    $output[$i] = $this->get(Zend_Date::WEEKDAY_NARROW, $locale);
                    break;

                case 'EEEE' :
                    $output[$i] = $this->get(Zend_Date::WEEKDAY, $locale);
                    break;

                case 'EEE' :
                    $output[$i] = $this->get(Zend_Date::WEEKDAY_SHORT, $locale);
                    break;

                case 'EE' :
                    $output[$i] = $this->get(Zend_Date::WEEKDAY_NAME, $locale);
                    break;

                case 'E' :
                    $output[$i] = $this->get(Zend_Date::WEEKDAY_NARROW, $locale);
                    break;


                // weekday number
                case 'ee' :
                    $output[$i] = str_pad($this->get(Zend_Date::WEEKDAY_8601, $locale), 2, '0', STR_PAD_LEFT);
                    break;

                case 'e' :
                    $output[$i] = $this->get(Zend_Date::WEEKDAY_8601, $locale);
                    break;


                // period
                case 'a' :
                    $output[$i] = $this->get(Zend_Date::MERIDIEM, $locale);
                    break;


                // hour
                case 'hh' :
                    $output[$i] = $this->get(Zend_Date::HOUR_AM, $locale);
                    break;

                case 'h' :
                    $output[$i] = $this->get(Zend_Date::HOUR_SHORT_AM, $locale);
                    break;

                case 'HH' :
                    $output[$i] = $this->get(Zend_Date::HOUR, $locale);
                    break;

                case 'H' :
                    $output[$i] = $this->get(Zend_Date::HOUR_SHORT, $locale);
                    break;


                // minute
                case 'mm' :
                    $output[$i] = $this->get(Zend_Date::MINUTE, $locale);
                    break;

                case 'm' :
                    $output[$i] = $this->get(Zend_Date::MINUTE_SHORT, $locale);
                    break;


                // second
                case 'ss' :
                    $output[$i] = $this->get(Zend_Date::SECOND, $locale);
                    break;

                case 's' :
                    $output[$i] = $this->get(Zend_Date::SECOND_SHORT, $locale);
                    break;

                case 'S' :
                    $output[$i] = $this->get(Zend_Date::MILLISECOND, $locale);
                    break;


                // zone
                case 'zzzz' :
                    $output[$i] = $this->get(Zend_Date::TIMEZONE_NAME, $locale);
                    break;

                case 'zzz' :
                case 'zz'  :
                case 'z'   :
                    $output[$i] = $this->get(Zend_Date::TIMEZONE, $locale);
                    break;


                // zone offset
                case 'ZZZZ' :
                    $output[$i] = $this->get(Zend_Date::GMT_DIFF_SEP, $locale);
                    break;

                case 'ZZZ' :
                case 'ZZ'  :
                case 'Z'   :
                    $output[$i] = $this->get(Zend_Date::GMT_DIFF, $locale);
                    break;
                    
                default :
                    $notset = true;
                    break;
            }

            // fill variable tokens
            if ($notset == true) {
                if (($output[$i][0] !== "'") and (preg_match('/y+/', $output[$i]))) {
                    $length     = strlen($output[$i]);
                    $output[$i] = $this->get(Zend_Date::YEAR, $locale);
                    $output[$i] = str_pad($output[$i], $length, '0', STR_PAD_LEFT);
                }

                if (($output[$i][0] !== "'") and (preg_match('/Y+/', $output[$i]))) {
                    $length     = strlen($output[$i]);
                    $output[$i] = $this->get(Zend_Date::YEAR_8601, $locale);
                    $output[$i] = str_pad($output[$i], $length, '0', STR_PAD_LEFT);
                }

                if (($output[$i][0] !== "'") and (preg_match('/A+/', $output[$i]))) {
                    $length     = strlen($output[$i]);
                    $seconds    = $this->get(Zend_Date::TIMESTAMP,   $locale);
                    $month      = $this->get(Zend_Date::MONTH_DIGIT, $locale);
                    $day        = $this->get(Zend_Date::DAY_SHORT,   $locale);
                    $year       = $this->get(Zend_Date::YEAR,        $locale);

                    $gmt = $this->getGmt();
                    if ($gmt == -1) {
                        $gmt = false;
                    }
                    $seconds   -= $this->mktime(0, 0, 0, $month, $day, $year, $this->_DST, $gmt);
                    $output[$i] = str_pad($seconds, $length, '0', STR_PAD_LEFT);
                }

                if ($output[$i][0] === "'") {
                    $output[$i] = substr($output[$i], 1);
                }
            }
            $notset = false;
        }

        return implode('', $output);
    }


    /**
     * Returns a string representation of the date which is equal with the timestamp
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString(null, $this->_Locale);
    }


    /**
     * Returns a integer representation of the object
     * But returns false when the given part is no value f.e. Month-Name
     *
     * @param  string|integer|Zend_Date  $part  OPTIONAL Defines the date or datepart to return as integer
     * @return integer|false
     */
    public function toValue($part = null)
    {
        $result = $this->get($part);
        if (is_numeric($result)) {
          return intval("$result");
        } else {
          return false;
        }
    }


    /**
     * Returns a representation of a date or datepart
     * This could be for example a localized monthname, the time without date,
     * the era or only the fractional seconds. There are about 50 different supported date parts.
     * For a complete list of supported datepart values look into the docu
     *
     * @param  string              $part    OPTIONAL Part of the date to return, if null the timestamp is returned
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return integer|string  date or datepart
     */
    public function get($part = null, $locale = null)
    {
        if ($part === null) {
            $part = Zend_Date::TIMESTAMP;
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }

        $gmt = $this->_setGmt($this->_GMT, false);
        switch($part) {

            // day formats
            case Zend_Date::DAY :
                return $this->date('d', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::WEEKDAY_SHORT :
                $weekday = strtolower($this->date('D', $this->getUnixTimestamp(), $gmt));
                $day = Zend_Locale_Data::getContent($locale, 'day', array('gregorian', 'wide', $weekday));
                return substr($day[$weekday], 0, 3);
                break;

            case Zend_Date::DAY_SHORT :
                return $this->date('j', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::WEEKDAY :
                $weekday = strtolower($this->date('D', $this->getUnixTimestamp(), $gmt));
                $day = Zend_Locale_Data::getContent($locale, 'day', array('gregorian', 'wide', $weekday));
                return $day[$weekday];
                break;

            case Zend_Date::WEEKDAY_8601 :
                return $this->date('N', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::DAY_SUFFIX :
                return $this->date('S', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::WEEKDAY_DIGIT :
                return $this->date('w', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::DAY_OF_YEAR :
                return $this->date('z', $this->getUnixTimestamp(), $gmt);
                break;


            case Zend_Date::WEEKDAY_NARROW :
                $weekday = strtolower($this->date('D', $this->getUnixTimestamp(), $gmt));
                $day = Zend_Locale_Data::getContent($locale, 'day', array('gregorian', 'abbreviated', $weekday));
                return substr($day[$weekday], 0, 1);
                break;

            case Zend_Date::WEEKDAY_NAME :
                $weekday = strtolower($this->date('D', $this->getUnixTimestamp(), $gmt));
                $day = Zend_Locale_Data::getContent($locale, 'day', array('gregorian', 'abbreviated', $weekday));
                return $day[$weekday];
                break;


            // week formats
            case Zend_Date::WEEK :
                return $this->date('W', $this->getUnixTimestamp(), $gmt);
                break;


            // month formats
            case Zend_Date::MONTH :
                $month = $this->date('n', $this->getUnixTimestamp(), $gmt);
                $mon = Zend_Locale_Data::getContent($locale, 'month', array('gregorian', 'format', 'wide', $month));
                return $mon[$month];
                break;

            case Zend_Date::MONTH_SHORT :
                return $this->date('m', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::MONTH_NAME :
                $month = $this->date('n', $this->getUnixTimestamp(), $gmt);
                $mon = Zend_Locale_Data::getContent($locale, 'month', array('gregorian', 'format', 'abbreviated', $month));
                return $mon[$month];
                break;

            case Zend_Date::MONTH_DIGIT :
                return $this->date('n', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::MONTH_DAYS :
                return $this->date('t', $this->getUnixTimestamp(), $gmt);
                break;


            case Zend_Date::MONTH_NARROW :
                $month = $this->date('n', $this->getUnixTimestamp(), $gmt);
                $mon = Zend_Locale_Data::getContent($locale, 'month', array('gregorian', 'format', 'abbreviated', $month));
                return substr($mon[$month], 0, 1);
                break;


            // year formats
            case Zend_Date::LEAPYEAR :
                return $this->date('L', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::YEAR_8601 :
                return $this->date('o', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::YEAR :
                return $this->date('Y', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::YEAR_SHORT :
                return $this->date('y', $this->getUnixTimestamp(), $gmt);
                break;


            case Zend_Date::YEAR_SHORT_8601 :
                $year = $this->date('o', $this->getUnixTimestamp(), $gmt);
                return substr($year, -2);
                break;


            // time formats
            case Zend_Date::MERIDIEM :
                $am = $this->date('a', $this->getUnixTimestamp(), $gmt);
                $amlocal = Zend_Locale_Data::getContent($locale, 'daytime', 'gregorian');
                return $amlocal[$am];
                break;

            case Zend_Date::SWATCH :
                return $this->date('B', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::HOUR_SHORT_AM :
                return $this->date('g', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::HOUR_SHORT :
                return $this->date('G', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::HOUR_AM :
                return $this->date('h', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::HOUR :
                return $this->date('H', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::MINUTE :
                return $this->date('i', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::SECOND :
                return $this->date('s', $this->getUnixTimestamp(), $gmt);
                break;


            case Zend_Date::MINUTE_SHORT :
                return $this->date('i', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::SECOND_SHORT :
                return $this->date('s', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::MILLISECOND :
                return $this->_Fractional;
                break;


            // timezone formats
            case Zend_Date::TIMEZONE_NAME :
                return $this->date('e', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::DAYLIGHT :
                return $this->date('I', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::GMT_DIFF :
                return $this->date('O', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::GMT_DIFF_SEP :
                return $this->date('P', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::TIMEZONE :
                return $this->date('T', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::TIMEZONE_SECS :
                return $this->date('Z', $this->getUnixTimestamp(), $gmt);
                break;


            // date strings
            case Zend_Date::ISO_8601 :
                return $this->date('c', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::RFC_2822 :
                return $this->date('r', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::TIMESTAMP :
                return $this->getUnixTimestamp();
                break;


            // additional formats
            case Zend_Date::ERA :
                $year = $this->date('Y', $this->getUnixTimestamp(), $gmt);
                if ($year < 0) {
                    $era = Zend_Locale_Data::getContent($locale, 'erashort', array('gregorian', '0'));
                    return $era['0'];
                }
                $era = Zend_Locale_Data::getContent($locale, 'erashort', array('gregorian', '1'));
                return $era['1'];
                break;

            case Zend_Date::ERA_NAME :
                $year = $this->date('Y', $this->getUnixTimestamp(), $gmt);
                if ($year < 0) {
                    $era = Zend_Locale_Data::getContent($locale, 'era', array('gregorian', '0'));
                    return $era['0'];
                }
                $era = Zend_Locale_Data::getContent($locale, 'era', array('gregorian', '1'));
                if (!isset($era['1'])) {
                    return false;
                }
                return $era['1'];
                break;

            case Zend_Date::DATES :
                $default = Zend_Locale_Data::getContent($locale, 'defdateformat', 'gregorian');
                $date = Zend_Locale_Data::getContent($locale, 'dateformat', array('gregorian', $default['default']));
                return $this->toString($date['pattern'], $locale);
                break;

            case Zend_Date::DATE_FULL :
                $date = Zend_Locale_Data::getContent($locale, 'dateformat', array('gregorian', 'full'));
                return $this->toString($date['pattern'], $locale);
                break;

            case Zend_Date::DATE_LONG :
                $date = Zend_Locale_Data::getContent($locale, 'dateformat', array('gregorian', 'long'));
                return $this->toString($date['pattern'], $locale);
                break;

            case Zend_Date::DATE_MEDIUM :
                $date = Zend_Locale_Data::getContent($locale, 'dateformat', array('gregorian', 'medium'));
                return $this->toString($date['pattern'], $locale);
                break;

            case Zend_Date::DATE_SHORT :
                $date = Zend_Locale_Data::getContent($locale, 'dateformat', array('gregorian', 'short'));
                return $this->toString($date['pattern'], $locale);
                break;

            case Zend_Date::TIMES :
                $default = Zend_Locale_Data::getContent($locale, 'deftimeformat', 'gregorian');
                $time = Zend_Locale_Data::getContent($locale, 'timeformat', array('gregorian', $default['default']));
                return $this->toString($time['pattern'], $locale);
                break;

            case Zend_Date::TIME_FULL :
                $time = Zend_Locale_Data::getContent($locale, 'timeformat', array('gregorian', 'full'));
                return $this->toString($time['pattern'], $locale);
                break;

            case Zend_Date::TIME_LONG :
                $time = Zend_Locale_Data::getContent($locale, 'timeformat', array('gregorian', 'long'));
                return $this->toString($time['pattern'], $locale);
                break;

            case Zend_Date::TIME_MEDIUM :
                $time = Zend_Locale_Data::getContent($locale, 'timeformat', array('gregorian', 'medium'));
                return $this->toString($time['pattern'], $locale);
                break;

            case Zend_Date::TIME_SHORT :
                $time = Zend_Locale_Data::getContent($locale, 'timeformat', array('gregorian', 'short'));
                return $this->toString($time['pattern'], $locale);
                break;

            case Zend_Date::ATOM :
                return $this->date('Y\-m\-d\TH\:i\:sP', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::COOKIE :
                return $this->date('l\, d\-M\-y H\:i\:s e', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::RFC_822 :
                return $this->date('D\, d M y H\:i\:s O', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::RFC_850 :
                return $this->date('l\, d\-M\-y H\:i\:s e', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::RFC_1036 :
                return $this->date('D\, d M y H\:i\:s O', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::RFC_1123 :
                return $this->date('D\, d M Y H\:i\:s O', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::RFC_3339 :
                return $this->date('Y\-m\-d\TH\:i\:sP', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::RSS :
                return $this->date('D\, d M Y H\:i\:s O', $this->getUnixTimestamp(), $gmt);
                break;

            case Zend_Date::W3C :
                return $this->date('Y\-m\-d\TH\:i\:sP', $this->getUnixTimestamp(), $gmt);
                break;


            default :
                return $this->date($part, $this->getUnixTimestamp(), $gmt);
                break;
        }
    }


    /**
     * Return digit from standard names (english)
     * Faster implementation than locale aware searching
     */
    private function getDigitFromName($name)
    {
        switch($name) {
            case "Jan":
                return 1;

            case "Feb":
                return 2;

            case "Mar":
                return 3;

            case "Apr":
                return 4;

            case "May":
                return 5;

            case "Jun":
                return 6;

            case "Jul":
                return 7;

            case "Aug":
                return 8;

            case "Sep":
                return 9;

            case "Oct":
                return 10;

            case "Nov":
                return 11;

            case "Dec":
                return 12;
        }
    }


    /**
     * Sets the given date as new date or a given datepart as new datepart returning the new datepart 
     * This could be for example a localized dayname, the date without time,
     * the month or only the seconds. There are about 50 different supported date parts.
     * For a complete list of supported datepart values look into the docu
     *
     * @param  string|integer|Zend_Date  $date    Date or datepart to set 
     * @param  string                    $part    OPTIONAL Part of the date to set, if null the timestamp is set
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|string  new datepart
     * @throws Zend_Date_Exception
     */
    public function set($date, $part = null, $locale = null)
    {
        $this->_setGmt(false);
        $result = $this->_calculate('set', $date, $part, $locale);
        $this->_resetGmt();
        return $result;
    }


    /**
     * Adds a date or datepart to the existing date
     * This could be for example a ISO 8601 date, the hour,
     * the monthname or only the minute. There are about 50 different supported date parts.
     * For a complete list of supported datepart values look into the docu
     *
     * @param  string|integer|Zend_Date  $date    Date or datepart to add 
     * @param  string                    $part    OPTIONAL Part of the date to add, if null the timestamp is added
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|string  new datepart
     * @throws Zend_Date_Exception
     */
    public function add($date, $part = null, $locale = null)
    {
        $this->_setGmt(true);
        $this->_calculate('add', $date, $part, $locale);
        $result = $this->get($part, $locale);

        $this->_resetGmt();
        return $result;
    }


    /**
     * Subtracts a date from another date.
     * This could be for example a RFC2822 date, the time,
     * the year or only the timestamp. There are about 50 different supported date parts.
     * For a complete list of supported datepart values look into the docu
     * Be aware: Adding -2 Months is not equal to Subtracting 2 Months !!! 
     * 
     * @param  string|integer|Zend_Date  $date    Date or datepart to subtract
     * @param  string                    $part    OPTIONAL Part of the date to sub, if null the timestamp is subtracted
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|string  new datepart
     * @throws Zend_Date_Exception
     */
    public function sub($date, $part = null, $locale = null)
    {
        $this->_setGmt(true);
        $this->_calculate('sub', $date, $part, $locale);
        $result = $this->get($part, $locale);

        $this->_resetGmt();
        return $result;
    }


    /**
     * Compares a date or datepart with the existing one. 
     * Returns -1 if earlier, 0 if equal and 1 if later.
     *
     * @param  string|integer|Zend_Date  $date    Date or datepart to compare with the date object
     * @param  string                    $part    OPTIONAL Part of the date to compare, if null the timestamp is subtracted
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compare($date, $part = null, $locale = null)
    {
        $this->_setGmt(false);
        $compare = $this->_calculate('cmp', $date, $part, $locale);
        $this->_resetGmt();

        if ($compare > 0) {
            return 1;
        } else if ($compare < 0) {
            return -1;
        }
        return 0;
    }


    /**
     * Returns a new instance of Zend_Date with the selected part copied.
     * To make an exact copy, use PHP's clone keyword.
     * For a complete list of supported date part values look into the docu.
     * If a date part is copied, all other date parts are set to standard values.
     * For example: If only YEAR is copied, the returned date object is equal to
     * 01-01-YEAR 00:00:00 (01-01-1970 00:00:00 is equal to timestamp 0)
     * If only HOUR is copied, the returned date object is equal to
     * 01-01-1970 HOUR:00:00 (so $this contains a timestamp equal to a timestamp of 0 plus HOUR).
     * 
     * @param  string              $part    Part of the date to compare, if null the timestamp is subtracted
     * @param  string|Zend_Locale  $locale  OPTIONAL New object's locale.  No adjustments to timezone are made.
     * @return Zend_Date
     */
    public function copyPart($part, $locale = null)
    {
        $clone = clone $this;           // copy all instance variables
        $clone->setUnixTimestamp(0);    // except the timestamp
        if ($locale != null) {
            $clone->setLocale($locale); // set an other locale if selected
        }
        $clone->set($this, $part);
        return $clone;
    }


    /**
     * Calculates the date or object
     *
     * @param  string                    $calc  Calculation to make
     * @param  string|integer            $date  Date for calculation
     * @param  string|integer            $comp  Second date for calculation
     * @return integer|string|Zend_Date  new timestamp or Zend_Date depending on calculation
     */
    private function _assign($calc, $date, $comp = 0)
    {
        switch ($calc) {
            case 'add' :
                $this->setUnixTimestamp(call_user_func(Zend_Locale_Math::$add, $this->getUnixTimestamp(), $date));
                return $this->getUnixTimestamp();
                break;
            case 'sub' :
                $this->setUnixTimestamp(call_user_func(Zend_Locale_Math::$sub, $this->getUnixTimestamp(), $date));
                return $this->getUnixTimestamp();
                break;
            case 'cmp' :
                return call_user_func(Zend_Locale_Math::$comp, $comp, $date);
                break;
            case 'copy' :
                return new Zend_Date($date, Zend_Date::TIMESTAMP, $this->_GMT);
                break;
            case 'set' :
                $this->setUnixTimestamp(call_user_func(Zend_Locale_Math::$sub, $this->getUnixTimestamp(), $comp));
                $this->setUnixTimestamp(call_user_func(Zend_Locale_Math::$add, $this->getUnixTimestamp(), $date));
                return $this->getUnixTimestamp();
                break;
        }
    }


    /**
     * Calculates the date or object
     *
     * @param  string                    $calc    Calculation to make, one of: 'add'|'sub'|'cmp'|'copy'|'set'
     * @param  string|integer|Zend_Date  $date    Date or datepart to calculate with
     * @param  string                    $part    Part of the date to calculate, if null the timestamp is used
     * @param  string|Zend_Locale        $locale  Locale for parsing input
     * @return integer|string|Zend_Date  new timestamp
     * @throws Zend_Date_Exception
     */
    private function _calculate($calc, $date, $part, $locale)
    {
        if (is_null($date)) {
            throw new Zend_Date_Exception('parameter $date must be set, null is not allowed');
        }

        $type = gettype($part);
        if (($type === 'object' && ($part instanceof Zend_Locale)) or
            ($type === 'string' && (Zend_Locale::isLocale($part)))) {
            $locale = $part;
            $part = null;
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }
        
        if ($locale instanceof Zend_Locale) {
            $locale = $locale->toString();
        }

        // create date parts
        $year   = $this->get(Zend_Date::YEAR);
        $month  = $this->get(Zend_Date::MONTH_DIGIT);
        $day    = $this->get(Zend_Date::DAY_SHORT);
        $hour   = $this->get(Zend_Date::HOUR_SHORT);
        $minute = $this->get(Zend_Date::MINUTE_SHORT);
        $second = $this->get(Zend_Date::SECOND_SHORT);

        // if object extract value
        if ($date instanceof Zend_Date) {
            $date = $date->get($part, $locale);
        }

        // $date as object, part of foreign date as own date
        switch($part) {

            // day formats
            case Zend_Date::DAY :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1, 1 + intval($date), 1970, $this->_DST, $this->_GMT),
                                                 $this->mktime(0, 0, 0, 1, 1 + intval($day),  1970, $this->_DST, $this->_GMT));
                }
                throw new Zend_Date_Exception("invalid date ($date) operand, day expected", $date);
                break;

            case Zend_Date::WEEKDAY_SHORT :
                $daylist = Zend_Locale_Data::getContent($locale, 'daylist', array('gregorian', 'wide'));
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $locale);
                $cnt = 0;

                foreach ($daylist as $key => $value) {
                    if (strtoupper(substr($value, 0, 3)) == strtoupper($date)) {
                         $found = $cnt;
                        break;
                    }
                    ++$cnt;
                }

                // Weekday found
                if ($cnt < 7) {
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1, 1 + $found,   1970, $this->_DST, $this->_GMT),
                                                 $this->mktime(0, 0, 0, 1, 1 + $weekday, 1970, $this->_DST, $this->_GMT));
                }

                // Weekday not found
                throw new Zend_Date_Exception("invalid date ($date) operand, weekday expected", $date);
                break;

            case Zend_Date::DAY_SHORT :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1, 1 + intval($date), 1970, $this->_DST, $this->_GMT),
                                                 $this->mktime(0, 0, 0, 1, 1 + intval($day),  1970, $this->_DST, $this->_GMT));
                }
                throw new Zend_Date_Exception("invalid date ($date) operand, day expected", $date);
                break;

            case Zend_Date::WEEKDAY :
                $daylist = Zend_Locale_Data::getContent($locale, 'daylist', array('gregorian', 'wide'));
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $locale);
                $cnt = 0;

                foreach ($daylist as $key => $value) {
                    if (strtoupper($value) == strtoupper($date)) {
                        $found = $cnt;
                        break;
                    }
                    ++$cnt;
                }

                // Weekday found
                if ($cnt < 7) {
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1, 1 + $found,   1970, $this->_DST, $this->_GMT),
                                                 $this->mktime(0, 0, 0, 1, 1 + $weekday, 1970, $this->_DST, $this->_GMT));
                }

                // Weekday not found
                throw new Zend_Date_Exception("invalid date ($date) operand, weekday expected", $date);
                break;

            case Zend_Date::WEEKDAY_8601 :
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $locale);
                if ((intval($date) > 0) and (intval($date) < 8)) {
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1, 1 + intval($date), 1970, $this->_DST, $this->_GMT),
                                                 $this->mktime(0, 0, 0, 1, 1 + $weekday,      1970, $this->_DST, $this->_GMT));
                }

                // Weekday not found
                throw new Zend_Date_Exception("invalid date ($date) operand, weekday expected", $date);
                break;

            case Zend_Date::DAY_SUFFIX :
                throw new Zend_Date_Exception('day suffix not supported', $date);
                break;

            case Zend_Date::WEEKDAY_DIGIT :
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $locale);
                if ((intval($date) > 0) and (intval($date) < 8)) {
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1, 2 + $date,    1970, $this->_DST, $this->_GMT),
                                                 $this->mktime(0, 0, 0, 1, 1 + $weekday, 1970, $this->_DST, $this->_GMT));
                }

                // Weekday not found
                throw new Zend_Date_Exception("invalid date ($date) operand, weekday expected", $date);
                break;

            case Zend_Date::DAY_OF_YEAR :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1,      1 + $date, 1970, $this->_DST, $this->_GMT),
                                                 $this->mktime(0, 0, 0, $month, 1 + $day,  1970, $this->_DST, $this->_GMT));
                }
                throw new Zend_Date_Exception("invalid date ($date) operand, day expected", $date);
                break;

            case Zend_Date::WEEKDAY_NARROW :
                $daylist = Zend_Locale_Data::getContent($locale, 'daylist', array('gregorian', 'abbreviated'));
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $locale);
                $cnt = 0;
                foreach ($daylist as $key => $value) {
                    if (strtoupper(substr($value, 0, 1)) == strtoupper($date)) {
                        $found = $cnt;
                        break;
                    }
                    ++$cnt;
                }

                // Weekday found
                if ($cnt < 7) {
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1, 1 + $found,   1970, $this->_DST, $this->_GMT),
                                                 $this->mktime(0, 0, 0, 1, 1 + $weekday, 1970, $this->_DST, $this->_GMT));
                }

                // Weekday not found
                throw new Zend_Date_Exception("invalid date ($date) operand, weekday expected", $date);
                break;

            case Zend_Date::WEEKDAY_NAME :
                $daylist = Zend_Locale_Data::getContent($locale, 'daylist', array('gregorian', 'abbreviated'));
                $weekday = (int) $this->get(Zend_Date::WEEKDAY_DIGIT, $locale);
                $cnt = 0;
                foreach ($daylist as $key => $value) {
                    if (strtoupper($value) == strtoupper($date)) {
                        $found = $cnt;
                        break;
                    }
                    ++$cnt;
                }

                // Weekday found
                if ($cnt < 7) {
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1, 1 + $found,   1970, $this->_DST, $this->_GMT),
                                                 $this->mktime(0, 0, 0, 1, 1 + $weekday, 1970, $this->_DST, $this->_GMT));
                }

                // Weekday not found
                throw new Zend_Date_Exception("invalid date ($date) operand, weekday expected", $date);
                break;


            // week formats
            case Zend_Date::WEEK :
                if (is_numeric($date)) {
                    $week = (int) $this->get(Zend_Date::WEEK, $locale);
                    return $this->_assign($calc, parent::mktime(0, 0, 0, 1, 1 + ($date * 7), 1970, $this->_DST, $this->_GMT),
                                                 parent::mktime(0, 0, 0, 1, 1 + ($week * 7), 1970, $this->_DST, $this->_GMT));
                }
                throw new Zend_Date_Exception("invalid date ($date) operand, week expected", $date);
                break;


            // month formats
            case Zend_Date::MONTH :
                $monthlist = Zend_Locale_Data::getContent($locale, 'monthlist', array('gregorian', 'wide'));
                $monthnr = (int) ($this->get(Zend_Date::MONTH_DIGIT, $locale));
                $cnt = 0;
                foreach ($monthlist as $key => $value) {
                    if (strtoupper($value) == strtoupper($date)) {
                        $found = $key;
                        break;
                    }
                    ++$cnt;
                }

                // Monthname found
                if ($cnt < 12) {
                    if ($calc == 'set') {
                        --$found;
                        --$monthnr;
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1 + $found,   1, 1970, $this->_DST, $this->_GMT),
                                                 $this->mktime(0, 0, 0, 1 + $monthnr, 1, 1970, $this->_DST, $this->_GMT));
                }

                // Monthname not found
                throw new Zend_Date_Exception("invalid date ($date) operand, month expected", $date);
                break;

            case Zend_Date::MONTH_SHORT :
                if (is_numeric($date)) {
                    if ($calc == 'set') {
                        --$date;
                        --$month;
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1 + intval($date), 1, 1970, $this->_DST, $this->_GMT),
                                                 $this->mktime(0, 0, 0, 1 + $month,        1, 1970, $this->_DST, $this->_GMT));
                }
                throw new Zend_Date_Exception("invalid date ($date) operand, month expected", $date);
                break;

            case Zend_Date::MONTH_NAME :
                $monthlist = Zend_Locale_Data::getContent($locale, 'monthlist', array('gregorian', 'abbreviated'));
                $monthnr = (int) ($this->get(Zend_Date::MONTH_DIGIT, $locale));
                $cnt = 0;
                foreach ($monthlist as $key => $value) {
                    if (strtoupper($value) == strtoupper($date)) {
                        $found = $key;
                        break;
                    }
                    ++$cnt;
                }

                // Monthname found
                if ($cnt < 12) {
                    if ($calc == 'set') {
                        --$found;
                        --$monthnr;
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1 + $found,   1, 1970, $this->_DST, $this->_GMT),
                                                 $this->mktime(0, 0, 0, 1 + $monthnr, 1, 1970, $this->_DST, $this->_GMT));
                }

                // Monthname not found
                throw new Zend_Date_Exception("invalid date ($date) operand, month expected", $date);
                break;

            case Zend_Date::MONTH_DIGIT :
                if (is_numeric($date)) {
                    if ($calc == 'set') {
                        --$date;
                        --$month;
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1 + intval($date), 1, 1970, $this->_DST, $this->_GMT),
                                                 $this->mktime(0, 0, 0, 1 + $month,        1, 1970, $this->_DST, $this->_GMT));
                }
                throw new Zend_Date_Exception("invalid date ($date) operand, month expected", $date);
                break;

            case Zend_Date::MONTH_DAYS :
                throw new Zend_Date_Exception('month days not supported', $date);
                break;


            case Zend_Date::MONTH_NARROW :
                $monthlist = Zend_Locale_Data::getContent($locale, 'monthlist', array('gregorian', 'abbreviated'));
                $monthnr = (int) ($this->get(Zend_Date::MONTH_DIGIT, $locale));
                $cnt = 0;
                foreach ($monthlist as $key => $value) {
                    if (strtoupper(substr($value, 0, 1)) == strtoupper($date)) {
                        $found = $key;
                        break;
                    }
                    ++$cnt;
                }

                // Monthname found
                if ($cnt < 12) {
                    if ($calc == 'set') {
                        --$found;
                        --$monthnr;
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1 + $found,   1, 1970, $this->_DST, $this->_GMT),
                                                 $this->mktime(0, 0, 0, 1 + $monthnr, 1, 1970, $this->_DST, $this->_GMT));
                }

                // Monthname not found
                throw new Zend_Date_Exception("invalid date ($date) operand, month expected", $date);
                break;


            // year formats
            case Zend_Date::LEAPYEAR :
                throw new Zend_Date_Exception('leap year not supported', $date);
                break;

            case Zend_Date::YEAR_8601 :
                if (is_numeric($date)) {
                    if ($calc == 'set') {
                        $date -= 1970;
                        $year -= 1970;
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1, 1, 1970 + intval($date), $this->_DST, $this->_GMT),
                                                 $this->mktime(0, 0, 0, 1, 1, 1970 + $year,         $this->_DST, $this->_GMT));
                }
                throw new Zend_Date_Exception("invalid date ($date) operand, year expected", $date);
                break;

            case Zend_Date::YEAR :
                if (is_numeric($date)) {
                    if ($calc == 'set') {
                        $date -= 1970;
                        $year -= 1970;
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1, 1, 1970 + intval($date), $this->_DST, $this->_GMT),
                                                 $this->mktime(0, 0, 0, 1, 1, 1970 + $year,         $this->_DST, $this->_GMT));
                }
                throw new Zend_Date_Exception("invalid date ($date) operand, year expected", $date);
                break;

            case Zend_Date::YEAR_SHORT :
                if (is_numeric($date)) {
                    $date = intval($date);
                    if (($date >= 0) and ($date <= 100)) {
                        $date += 1900;
                        if ($date < 1970) {
                            $date += 100;
                        }
                        if ($calc == 'set') {
                            $date -= 1970;
                            $year -= 1970;
                        }
                        return $this->_assign($calc, $this->mktime(0, 0, 0, 1, 1, 1970 + $date, $this->_DST, $this->_GMT),
                                                     $this->mktime(0, 0, 0, 1, 1, 1970 + $year, $this->_DST, $this->_GMT));
                    }
                }
                throw new Zend_Date_Exception("invalid date ($date) operand, year expected", $date);
                break;


            case Zend_Date::YEAR_SHORT_8601 :
                if (is_numeric($date)) {
                    $date = intval($date);
                    if (($date >= 0) and ($date <= 100)) {
                        $date += 1900;
                        if ($date < 1970) {
                            $date += 100;
                        }
                        if ($calc == 'set') {
                            $date -= 1970;
                            $year -= 1970;
                        }
                        return $this->_assign($calc, $this->mktime(0, 0, 0, 1, 1, 1970 + $date, $this->_DST, $this->_GMT),
                                                     $this->mktime(0, 0, 0, 1, 1, 1970 + $year, $this->_DST, $this->_GMT));
                    }
                }
                throw new Zend_Date_Exception("invalid date ($date) operand, year expected", $date);
                break;


            // time formats
            case Zend_Date::MERIDIEM :
                throw new Zend_Date_Exception('meridiem not supported', $date);
                break;

            case Zend_Date::SWATCH :
                if (is_numeric($date)) {
                    $rest = intval($date);
                    $hours = floor($rest * 24 / 1000);
                    $rest = $rest - ($hours * 1000 / 24);
                    $minutes = floor($rest * 1440 / 1000);
                    $rest = $rest - ($minutes * 1000 / 1440);
                    $seconds = floor($rest * 86400 / 1000);
                    return $this->_assign($calc, $this->mktime($hours, $minutes, $seconds, 1, 1, 1970, $this->_DST, $this->_GMT),
                                                 $this->mktime($hour,  $minute,  $second,  1, 1, 1970, $this->_DST, $this->_GMT));
                }
                throw new Zend_Date_Exception("invalid date ($date) operand, swatchstamp expected", $date);
                break;

            case Zend_Date::HOUR_SHORT_AM :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(intval($date), 0, 0, 1, 1, 1970, $this->_DST, $this->_GMT),
                                                 $this->mktime($hour,         0, 0, 1, 1, 1970, $this->_DST, $this->_GMT));
                }
                throw new Zend_Date_Exception("invalid date ($date) operand, hour expected", $date);
                break;

            case Zend_Date::HOUR_SHORT :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(intval($date), 0, 0, 1, 1, 1970, $this->_DST, $this->_GMT),
                                                 $this->mktime($hour,         0, 0, 1, 1, 1970, $this->_DST, $this->_GMT));
                }
                throw new Zend_Date_Exception("invalid date ($date) operand, hour expected", $date);
                break;

            case Zend_Date::HOUR_AM :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(intval($date), 0, 0, 1, 1, 1970, $this->_DST, $this->_GMT),
                                                 $this->mktime($hour,         0, 0, 1, 1, 1970, $this->_DST, $this->_GMT));
                }
                throw new Zend_Date_Exception("invalid date ($date) operand, hour expected", $date);
                break;

            case Zend_Date::HOUR :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(intval($date), 0, 0, 1, 1, 1970, $this->_DST, $this->_GMT),
                                                 $this->mktime($hour,         0, 0, 1, 1, 1970, $this->_DST, $this->_GMT));
                }
                throw new Zend_Date_Exception("invalid date ($date) operand, hour expected", $date);
                break;

            case Zend_Date::MINUTE :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(0, intval($date), 0, 1, 1, 1970, $this->_DST, $this->_GMT),
                                                 $this->mktime(0, $minute,       0, 1, 1, 1970, $this->_DST, $this->_GMT));
                }
                throw new Zend_Date_Exception("invalid date ($date) operand, minute expected", $date);
                break;

            case Zend_Date::SECOND :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(0, 0, intval($date), 1, 1, 1970, $this->_DST, $this->_GMT),
                                                 $this->mktime(0, 0, $second,       1, 1, 1970, $this->_DST, $this->_GMT));
                }
                throw new Zend_Date_Exception("invalid date ($date) operand, second expected", $date);
                break;

            case Zend_Date::MILLISECOND :
                if (is_numeric($date)) {
                    switch($calc) {
                        case 'set' :
                            return $this->setMillisecond($date);
                            break;
                        case 'add' :
                            return $this->addMillisecond($date);
                            break;
                        case 'sub' :
                            return $this->subMillisecond($date);
                            break;
                    }
                    return $this->compareMillisecond($date);
                }
                throw new Zend_Date_Exception("invalid date ($date) operand, milliseconds expected", $date);
                break;

            case Zend_Date::MINUTE_SHORT :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(0, intval($date), 0, 1, 1, 1970, $this->_DST, $this->_GMT),
                                                 $this->mktime(0, $minute,       0, 1, 1, 1970, $this->_DST, $this->_GMT));
                }
                throw new Zend_Date_Exception("invalid date ($date) operand, minute expected", $date);
                break;

            case Zend_Date::SECOND_SHORT :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $this->mktime(0, 0, intval($date), 1, 1, 1970, $this->_DST, $this->_GMT),
                                                 $this->mktime(0, 0, $second,       1, 1, 1970, $this->_DST, $this->_GMT));
                }
                throw new Zend_Date_Exception("invalid date ($date) operand, second expected", $date);
                break;


            // timezone formats
            // break intentionally omitted
            case Zend_Date::TIMEZONE_NAME :
            case Zend_Date::TIMEZONE :
            case Zend_Date::TIMEZONE_SECS :
                throw new Zend_Date_Exception('timezone not supported', $date);
                break;

            case Zend_Date::DAYLIGHT :
                throw new Zend_Date_Exception('daylight not supported', $date);
                break;

            case Zend_Date::GMT_DIFF :
            case Zend_Date::GMT_DIFF_SEP :
                throw new Zend_Date_Exception('gmtdiff not supported', $date);
                break;


            // date strings
            case Zend_Date::ISO_8601 :

                $next = 0;
                if (preg_match('/-\d{4}-\d{2}-\d{2}/', $date, $datematch)) {
                    // -yyyy-mm-dd
                    $minus = true;
                    $result = array('Y' => 1, 'M' => 6, 'd' => 9);
                    $next = 11;
                } else if (preg_match('/\d{4}-\d{2}-\d{2}/', $date, $datematch)) {
                    // yyyy-mm-dd
                    $result = array('Y' => 0, 'M' => 5, 'd' => 8);
                    $next = 10;
                } else if (preg_match('/-\d{2}-\d{2}-\d{2}/', $date, $datematch)) {
                    // -yy-mm-dd
                    $minus = true;
                    $result = array('y' => 1, 'M' => 4, 'd' => 7);
                    $next = 9;
                } else if (preg_match('/\d{2}-\d{2}-\d{2}/', $date, $datematch)) {
                    // yy-mm-dd
                    $result = array('y' => 0, 'M' => 3, 'd' => 6);
                    $next = 8;
                } else if (preg_match('/-\d{8}/', $date, $datematch)) {
                    // -yyyymmdd
                    $minus = true;
                    $result = array('Y' => 1, 'M' => 5, 'd' => 7);
                    $next = 9;
                } else if (preg_match('/\d{8}/', $date, $datematch)) {
                    // yyyymmdd
                    $result = array('Y' => 0, 'M' => 4, 'd' => 6);
                    $next = 8;
                } else if (preg_match('/-\d{6}/', $date, $datematch)) {
                    // -yymmdd
                    $minus = true;
                    $result = array('y' => 1, 'M' => 3, 'd' => 5);
                    $next = 7;
                } else if (preg_match('/\d{6}/', $date, $datematch)) {
                    // yymmdd
                    $result = array('y' => 0, 'M' => 2, 'd' => 4);
                    $next = 6;
                }
                if (strlen($date) > $next) {
                    $date = substr($date, $next);
                    // Thh:mm:ss
                    if (preg_match('/[T,\s]{1}\d{2}:\d{2}:\d{2}/', $date, $timematch)) {
                        // Thh:mm:ss | _hh:mm:ss
                        $result['h'] = 1;
                        $result['m'] = 4;
                        $result['s'] = 7;
                        $next += 9;
                    } else if (preg_match('/\d{2}:\d{2}:\d{2}/', $date, $timematch)) {
                        // hh:mm:ss
                        $result['h'] = 0;
                        $result['m'] = 3;
                        $result['s'] = 6;
                        $next += 8;
                    } else if (preg_match('/[T,\s]{1}\d{2}\d{2}\d{2}/', $date, $timematch)) {
                        // Thhmmss | _hhmmss
                        $result['h'] = 1;
                        $result['m'] = 3;
                        $result['s'] = 5;
                        $next += 7;
                    } else if (preg_match('/\d{2}\d{2}\d{2}/', $date, $timematch)) {
                        // hhmmss | hhmmss
                        $result['h'] = 0;
                        $result['m'] = 2;
                        $result['s'] = 4;
                        $next += 6;
                    }
                }

                if (!isset($result)) {
                    throw new Zend_Date_Exception("unsupported ISO8601 format ($date)", $date);
                }

                if(isset($result['M'])) {
                    if (isset($result['Y'])) {
                        $years = substr($datematch[0], $result['Y'], 4);
                        if (isset($minus)) {
                            $years = 0 - $years;
                        }
                    } else {
                        $years = substr($datematch[0], $result['y'], 2);
                        if (isset($minus)) {
                            $years = 0 - $years;
                        }
                        if ($years >= 0) {
                            $years += 1900;
                            if ($years < 1970)
                                $years += 100;
                        }
                    }
                    $months  = substr($datematch[0], $result['M'], 2);
                    $days    = substr($datematch[0], $result['d'], 2);
                } else {
                    $years  = 1970;
                    $months = 1;
                    $days   = 1;
                }
                if (isset($result['h'])) {
                    $hours   = substr($timematch[0], $result['h'], 2);
                    $minutes = substr($timematch[0], $result['m'], 2);
                    $seconds = substr($timematch[0], $result['s'], 2);
                } else {
                    $hours   = 0;
                    $minutes = 0;
                    $seconds = 0;
                }

                if ($calc == 'set') {
                    --$months;
                    --$month;
                    --$days;
                    --$day;
                    $years -= 1970;
                    $year  -= 1970;
                }
                return $this->_assign($calc, $this->mktime($hours, $minutes, $seconds, 1 + $months, 1 + $days, 1970 + $years, $this->_DST, $this->_GMT),
                                             $this->mktime($hour,  $minute,  $second,  1 + $month,  1 + $day,  1970 + $year,  $this->_DST, $this->_GMT));
                break;

            case Zend_Date::RFC_2822 :
                $result = preg_match('/\w{3},\s\d{2}\s\w{3}\s\d{4}\s\d{2}:\d{2}:\d{2}\s\+\d{4}/', $date, $match);
                if (!$result) {
                    throw new Zend_Date_Exception("no RFC 2822 format ($date)", $date);
                }

                $days    = substr($match[0], 5, 2);
                $months  = $this->getDigitFromName(substr($match[0], 8, 3));
                $years   = substr($match[0], 12, 4);
                $hours   = substr($match[0], 17, 2);
                $minutes = substr($match[0], 20, 2);
                $seconds = substr($match[0], 23, 2);

                if ($calc == 'set') {
                    --$months;
                    --$month;
                    --$days;
                    --$day;
                    $years -= 1970;
                    $year  -= 1970;
                }
                return $this->_assign($calc, $this->mktime($hours, $minutes, $seconds, 1 + $months, 1 + $days, 1970 + $years, $this->_DST, $this->_GMT),
                                             $this->mktime($hour,  $minute,  $second,  1 + $month,  1 + $day,  1970 + $year,  $this->_DST, $this->_GMT));
                break;

            case Zend_Date::TIMESTAMP :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $date, $this->getUnixTimestamp());
                }
                throw new Zend_Date_Exception("invalid date ($date) operand, timestamp expected", $date);
                break;


            // additional formats
            // break intentionally omitted
            case Zend_Date::ERA :
            case Zend_Date::ERA_NAME :
                throw new Zend_Date_Exception('era not supported', $date);
                break;

            case Zend_Date::DATES :
                try {
                    $parsed = Zend_Locale_Format::getDate($date, false, $locale);

                    if ($calc == 'set') {
                        --$parsed['month'];
                        --$month;
                        --$parsed['day'];
                        --$day;
                        $parsed['year'] -= 1970;
                        $year  -= 1970;
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1 + $parsed['month'],
                                                             1 + $parsed['day'], 1970 + $parsed['year'], $this->_DST, $this->_GMT),
                                                 $this->mktime(0, 0, 0, 1 + $month, 1 + $day, 1970 + $year, $this->_DST, $this->_GMT));
                } catch (Zend_Locale_Exception $e) {
                    throw new Zend_Date_Exception($e->getMessage(), $date);
                }
                break;

            case Zend_Date::DATE_FULL :
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'dateformat', array('gregorian', 'full'));
                    $parsed = Zend_Locale_Format::getDate($date, $format['pattern'], $locale);

                    if ($calc == 'set') {
                        --$parsed['month'];
                        --$month;
                        --$parsed['day'];
                        --$day;
                        $parsed['year'] -= 1970;
                        $year  -= 1970;
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1 + $parsed['month'],
                                                             1 + $parsed['day'], 1970 + $parsed['year'], $this->_DST, $this->_GMT),
                                                 $this->mktime(0, 0, 0, 1 + $month, 1 + $day, 1970 + $year, $this->_DST, $this->_GMT));
                } catch (Zend_Locale_Exception $e) {
                    throw new Zend_Date_Exception($e->getMessage(), $date);
                }
                break;

            case Zend_Date::DATE_LONG :
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'dateformat', array('gregorian', 'long'));
                    $parsed = Zend_Locale_Format::getDate($date, $format['pattern'], $locale);

                    if ($calc == 'set') {
                        --$parsed['month'];
                        --$month;
                        --$parsed['day'];
                        --$day;
                        $parsed['year'] -= 1970;
                        $year  -= 1970;
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1 + $parsed['month'],
                                  1 + $parsed['day'], 1970 + $parsed['year'], $this->_DST, $this->_GMT),
                                  $this->mktime(0, 0, 0, 1 + $month, 1 + $day, 1970 + $year, $this->_DST, $this->_GMT));
                } catch (Zend_Locale_Exception $e) {
                    throw new Zend_Date_Exception($e->getMessage(), $date);
                }
                break;

            case Zend_Date::DATE_MEDIUM :
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'dateformat', array('gregorian', 'medium'));
                    $parsed = Zend_Locale_Format::getDate($date, $format['pattern'], $locale);

                    if ($calc == 'set') {
                        --$parsed['month'];
                        --$month;
                        --$parsed['day'];
                        --$day;
                        $parsed['year'] -= 1970;
                        $year  -= 1970;
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1 + $parsed['month'],
                                  1 + $parsed['day'], 1970 + $parsed['year'], $this->_DST, $this->_GMT),
                                  $this->mktime(0, 0, 0, 1 + $month, 1 + $day, 1970 + $year, $this->_DST, $this->_GMT));
                } catch (Zend_Locale_Exception $e) {
                    throw new Zend_Date_Exception($e->getMessage(), $date);
                }
                break;

            case Zend_Date::DATE_SHORT :
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'dateformat', array('gregorian', 'short'));
                    $parsed = Zend_Locale_Format::getDate($date, $format['pattern'], $locale);
                    if ($parsed['year'] < 100) {
                        $parsed['year'] += 1900;
                        if ($parsed['year'] < 1970) {
                            $parsed['year'] += 100;
                        }
                    }

                    if ($calc == 'set') {
                        --$parsed['month'];
                        --$month;
                        --$parsed['day'];
                        --$day;
                        $parsed['year'] -= 1970;
                        $year  -= 1970;
                    }
                    return $this->_assign($calc, $this->mktime(0, 0, 0, 1 + $parsed['month'],
                                  1 + $parsed['day'], 1970 + $parsed['year'], $this->_DST, $this->_GMT),
                                  $this->mktime(0, 0, 0, 1 + $month, 1 + $day, 1970 + $year, $this->_DST, $this->_GMT));
                } catch (Zend_Locale_Exception $e) {
                    throw new Zend_Date_Exception($e->getMessage(), $date);
                }
                break;

            case Zend_Date::TIMES :
                try {
                    $parsed = Zend_Locale_Format::getTime($date, false, $locale);
                    return $this->_assign($calc, $this->mktime($parsed['hour'], $parsed['minute'], $parsed['second'],
                                  1, 1, 1970, $this->_DST, $this->_GMT),
                                  $this->mktime($hour, $minute, $second, 1, 1, 1970, $this->_DST, $this->_GMT));
                } catch (Zend_Locale_Exception $e) {
                    throw new Zend_Date_Exception($e->getMessage(), $date);
                }
                break;

            case Zend_Date::TIME_FULL :
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'timeformat', array('gregorian', 'full'));
                    $parsed = Zend_Locale_Format::getTime($date, $format['pattern'], $locale);
                    return $this->_assign($calc, $this->mktime($parsed['hour'], $parsed['minute'], 0,
                                  1, 1, 1970, $this->_DST, $this->_GMT),
                                  $this->mktime($hour, $minute, $second, 1, 1, 1970, $this->_DST, $this->_GMT));
                } catch (Zend_Locale_Exception $e) {
                    throw new Zend_Date_Exception($e->getMessage(), $date);
                }
                break;

            case Zend_Date::TIME_LONG :
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'timeformat', array('gregorian', 'long'));
                    $parsed = Zend_Locale_Format::getTime($date, $format['pattern'], $locale);
                    return $this->_assign($calc, $this->mktime($parsed['hour'], $parsed['minute'], $parsed['second'],
                                  1, 1, 1970, $this->_DST, $this->_GMT),
                                  $this->mktime($hour, $minute, $second, 1, 1, 1970, $this->_DST, $this->_GMT));
                } catch (Zend_Locale_Exception $e) {
                    throw new Zend_Date_Exception($e->getMessage(), $date);
                }
                break;

            case Zend_Date::TIME_MEDIUM :
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'timeformat', array('gregorian', 'medium'));
                    $parsed = Zend_Locale_Format::getTime($date, $format['pattern'], $locale);
                    return $this->_assign($calc, $this->mktime($parsed['hour'], $parsed['minute'], $parsed['second'],
                                  1, 1, 1970, $this->_DST, $this->_GMT),
                                  $this->mktime($hour, $minute, $second, 1, 1, 1970, $this->_DST, $this->_GMT));
                } catch (Zend_Locale_Exception $e) {
                    throw new Zend_Date_Exception($e->getMessage(), $date);
                }
                break;

            case Zend_Date::TIME_SHORT :
                try {
                    $format = Zend_Locale_Data::getContent($locale, 'timeformat', array('gregorian', 'short'));
                    $parsed = Zend_Locale_Format::getTime($date, $format['pattern'], $locale);
                    return $this->_assign($calc, $this->mktime($parsed['hour'], $parsed['minute'], 0,
                                  1, 1, 1970, $this->_DST, $this->_GMT),
                                  $this->mktime($hour, $minute, $second, 1, 1, 1970, $this->_DST, $this->_GMT));
                } catch (Zend_Locale_Exception $e) {
                    throw new Zend_Date_Exception($e->getMessage(), $date);
                }
                break;

            // ATOM and RFC_3339 are identical
            case Zend_Date::ATOM :
            case Zend_Date::RFC_3339:
                $result = preg_match('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{2}:\d{2}/', $date, $match);
                if (!$result) {
                    throw new Zend_Date_Exception("invalid date ($date) operand, ATOM format expected", $date);
                }

                $years   = substr($match[0], 0, 4);
                $months  = substr($match[0], 5, 2);
                $days    = substr($match[0], 8, 2);
                $hours   = substr($match[0], 11, 2);
                $minutes = substr($match[0], 14, 2);
                $seconds = substr($match[0], 17, 2);

                if ($calc == 'set') {
                    --$months;
                    --$month;
                    --$days;
                    --$day;
                    $years -= 1970;
                    $year  -= 1970;
                }
                return $this->_assign($calc, $this->mktime($hours, $minutes, $seconds, 1 + $months, 1 + $days, 1970 + $years, $this->_DST, $this->_GMT),
                                             $this->mktime($hour,  $minute,  $second,  1 + $month,  1 + $day,  1970 + $year,  $this->_DST, $this->_GMT));
                break;

            case Zend_Date::COOKIE :
                $result = preg_match('/\w{6,9},\s\d{2}-\w{3}-\d{2}\s\d{2}:\d{2}:\d{2}\s\w{3}/', $date, $match);
                if (!$result) {
                    throw new Zend_Date_Exception("invalid date ($date) operand, COOKIE format expected", $date);
                }
                $match[0] = substr($match[0], strpos($match[0], ' ')+1);

                $days    = substr($match[0], 0, 2);
                $months  = $this->getDigitFromName(substr($match[0], 3, 3));
                $years   = substr($match[0], 7, 4);
                $years  += 2000;
                $hours   = substr($match[0], 10, 2);
                $minutes = substr($match[0], 13, 2);
                $seconds = substr($match[0], 16, 2);

                if ($calc == 'set') {
                    --$months;
                    --$month;
                    --$days;
                    --$day;
                    $years -= 1970;
                    $year  -= 1970;
                }
                return $this->_assign($calc, $this->mktime($hours, $minutes, $seconds, 1 + $months, 1 + $days, 1970 + $years, $this->_DST, $this->_GMT),
                                             $this->mktime($hour,  $minute,  $second,  1 + $month,  1 + $day,  1970 + $year,  $this->_DST, $this->_GMT));
                break;

            case Zend_Date::RFC_822 :
                $result = preg_match('/\w{3},\s\d{2}\s\w{3}\s\d{2}\s\d{2}:\d{2}:\d{2}\s\+\d{4}/', $date, $match);
                if (!$result) {
                    throw new Zend_Date_Exception("invalid date ($date) operand, RFC 822 date format expected", $date);
                }

                $days    = substr($match[0], 5, 2);
                $months  = $this->getDigitFromName(substr($match[0], 8, 3));
                $years   = substr($match[0], 12, 4);
                $years  += 2000;
                $hours   = substr($match[0], 15, 2);
                $minutes = substr($match[0], 18, 2);
                $seconds = substr($match[0], 21, 2);

                if ($calc == 'set') {
                    --$months;
                    --$month;
                    --$days;
                    --$day;
                    $years -= 1970;
                    $year  -= 1970;
                }
                return $this->_assign($calc, $this->mktime($hours, $minutes, $seconds, 1 + $months, 1 + $days, 1970 + $years, $this->_DST, $this->_GMT),
                                             $this->mktime($hour,  $minute,  $second,  1 + $month,  1 + $day,  1970 + $year,  $this->_DST, $this->_GMT));
                break;

            case Zend_Date::RFC_850 :
                $result = preg_match('/\w{6,9},\s\d{2}-\w{3}-\d{2}\s\d{2}:\d{2}:\d{2}\s\w{3}/', $date, $match);
                if (!$result) {
                    throw new Zend_Date_Exception("invalid date ($date) operand, RFC 850 date format expected", $date);
                }

                $match[0] = substr($match[0], strpos($match[0], ' ')+1);

                $days    = substr($match[0], 0, 2);
                $months  = $this->getDigitFromName(substr($match[0], 3, 3));
                $years   = substr($match[0], 7, 4);
                $years  += 2000;
                $hours   = substr($match[0], 10, 2);
                $minutes = substr($match[0], 13, 2);
                $seconds = substr($match[0], 16, 2);

                if ($calc == 'set') {
                    --$months;
                    --$month;
                    --$days;
                    --$day;
                    $years -= 1970;
                    $year  -= 1970;
                }
                return $this->_assign($calc, $this->mktime($hours, $minutes, $seconds, 1 + $months, 1 + $days, 1970 + $years, $this->_DST, $this->_GMT),
                                             $this->mktime($hour,  $minute,  $second,  1 + $month,  1 + $day,  1970 + $year,  $this->_DST, $this->_GMT));
                break;

            case Zend_Date::RFC_1036 :
                $result = preg_match('/\w{3},\s\d{2}\s\w{3}\s\d{2}\s\d{2}:\d{2}:\d{2}\s\+\d{4}/', $date, $match);
                if (!$result) {
                    throw new Zend_Date_Exception("invalid date ($date) operand, RFC 1036 date format expected", $date);
                }

                $days    = substr($match[0], 5, 2);
                $months  = $this->getDigitFromName(substr($match[0], 8, 3));
                $years   = substr($match[0], 12, 4);
                $years  += 2000;
                $hours   = substr($match[0], 15, 2);
                $minutes = substr($match[0], 18, 2);
                $seconds = substr($match[0], 21, 2);

                if ($calc == 'set') {
                    --$months;
                    --$month;
                    --$days;
                    --$day;
                    $years -= 1970;
                    $year  -= 1970;
                }
                return $this->_assign($calc, $this->mktime($hours, $minutes, $seconds, 1 + $months, 1 + $days, 1970 + $years, $this->_DST, $this->_GMT),
                                             $this->mktime($hour,  $minute,  $second,  1 + $month,  1 + $day,  1970 + $year,  $this->_DST, $this->_GMT));
                break;

            case Zend_Date::RFC_1123 :
                $result = preg_match('/\w{3},\s\d{2}\s\w{3}\s\d{4}\s\d{2}:\d{2}:\d{2}\s\+\d{4}/', $date, $match);
                if (!$result) {
                    throw new Zend_Date_Exception("invalid date ($date) operand, RFC 1123 date format expected", $date);
                }

                $days    = substr($match[0], 5, 2);
                $months  = $this->getDigitFromName(substr($match[0], 8, 3));
                $years   = substr($match[0], 12, 4);
                $hours   = substr($match[0], 17, 2);
                $minutes = substr($match[0], 20, 2);
                $seconds = substr($match[0], 23, 2);

                if ($calc == 'set') {
                    --$months;
                    --$month;
                    --$days;
                    --$day;
                    $years -= 1970;
                    $year  -= 1970;
                }
                return $this->_assign($calc, $this->mktime($hours, $minutes, $seconds, 1 + $months, 1 + $days, 1970 + $years, $this->_DST, $this->_GMT),
                                             $this->mktime($hour,  $minute,  $second,  1 + $month,  1 + $day,  1970 + $year,  $this->_DST, $this->_GMT));
                break;

            case Zend_Date::RSS :
                $result = preg_match('/\w{3},\s\d{2}\s\w{3}\s\d{4}\s\d{2}:\d{2}:\d{2}\s\+\d{4}/', $date, $match);
                if (!$result) {
                    throw new Zend_Date_Exception("invalid date ($date) operand, RSS date format expected", $date);
                }

                $days    = substr($match[0], 5, 2);
                $months  = $this->getDigitFromName(substr($match[0], 8, 3));
                $years   = substr($match[0], 12, 4);
                $hours   = substr($match[0], 17, 2);
                $minutes = substr($match[0], 20, 2);
                $seconds = substr($match[0], 23, 2);

                if ($calc == 'set') {
                    --$months;
                    --$month;
                    --$days;
                    --$day;
                    $years -= 1970;
                    $year  -= 1970;
                }
                return $this->_assign($calc, $this->mktime($hours, $minutes, $seconds, 1 + $months, 1 + $days, 1970 + $years, $this->_DST, $this->_GMT),
                                             $this->mktime($hour,  $minute,  $second,  1 + $month,  1 + $day,  1970 + $year,  $this->_DST, $this->_GMT));
                break;

            case Zend_Date::W3C :
                $result = preg_match('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{2}:\d{2}/', $date, $match);
                if (!$result) {
                    throw new Zend_Date_Exception("invalid date ($date) operand, W3C date format expected", $date);
                }

                $years   = substr($match[0], 0, 4);
                $months  = substr($match[0], 5, 2);
                $days    = substr($match[0], 8, 2);
                $hours   = substr($match[0], 11, 2);
                $minutes = substr($match[0], 14, 2);
                $seconds = substr($match[0], 17, 2);

                if ($calc == 'set') {
                    --$months;
                    --$month;
                    --$days;
                    --$day;
                    $years -= 1970;
                    $year  -= 1970;
                }
                return $this->_assign($calc, $this->mktime($hours, $minutes, $seconds, 1 + $months, 1 + $days, 1970 + $years, $this->_DST, $this->_GMT),
                                             $this->mktime($hour,  $minute,  $second,  1 + $month,  1 + $day,  1970 + $year,  $this->_DST, $this->_GMT));
                break;

            default :
                if (is_numeric($date)) {
                    return $this->_assign($calc, $date, $this->getUnixTimestamp());
                }
                throw new Zend_Date_Exception("invalid date ($date) operand, timestamp expected", $date);
                break;
        }
    }


    /**
     * Returns true when both date objects or date parts are equal.
     * For example:
     * 15.May.2000 <-> 15.June.2000 Equals only for Day or Year... all other will return false 
     *
     * @param  string|integer|Zend_Date  $date    Date or datepart to equal with
     * @param  string                    $part    OPTIONAL Part of the date to compare, if null the timestamp is used
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return boolean
     * @throws Zend_Date_Exception
     */
    public function equals($date, $part = null, $locale = null)
    {
        $this->_setGmt(false);
        $result = $this->compare($date, $part, $locale);
        $this->_resetGmt();

        if ($result == 0) {
            return true;
        }
        return false;
    }


    /**
     * Returns if the given date or datepart is earlier
     * For example:
     * 15.May.2000 <-> 13.June.1999 will return true for day, year and date, but not for month
     *
     * @param  string|integer|Zend_Date  $date    Date or datepart to compare with
     * @param  string                    $part    OPTIONAL Part of the date to compare, if null the timestamp is used
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return boolean
     * @throws Zend_Date_Exception
     */
    public function isEarlier($date, $part = null, $locale = null)
    {
        $this->_setGmt(false);
        $result = $this->compare($date, $part, $locale);
        $this->_resetGmt();

        if ($result == -1) {
            return true;
        }
        return false;
    }


    /**
     * Returns if the given date or datepart is later
     * For example:
     * 15.May.2000 <-> 13.June.1999 will return true for month but false for day, year and date
     * Returns if the given date is later
     *
     * @param  string|integer|Zend_Date  $date    Date or datepart to compare with
     * @param  string                    $part    OPTIONAL Part of the date to compare, if null the timestamp is used
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return boolean
     * @throws Zend_Date_Exception
     */
    public function isLater($date, $part = null, $locale = null)
    {
        $this->_setGmt(false);
        $result = $this->compare($date, $part, $locale);
        $this->_resetGmt();

        if ($result == 1) {
            return true;
        }
        return false;
    }


    /**
     * Returns only the time of the date as new Zend_Date object
     * For example: 
     * 15.May.2000 10:11:23 will return a dateobject equal to 01.Jan.1970 10:11:23
     * 
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getTime($locale = null)
    {
        return $this->copyPart(Zend_Date::TIME_MEDIUM, $locale);
    }


    /**
     * Returns the calculated time
     *
     * @param  string                    $calc    Calculation to make
     * @param  string|integer|Zend_Date  $time    Time to calculate with, if null the actual time is taken
     * @param  string                    $format  Timeformat for parsing input
     * @param  string|Zend_Locale        $locale  Locale for parsing input
     * @return integer|Zend_Date  new time
     * @throws Zend_Date_Exception
     */
    private function _time($calc, $time, $format, $locale)
    {
        if (is_null($time)) {
            throw new Zend_Date_Exception('parameter $time must be set, null is not allowed');
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }

        if (($calc == 'set') or ($calc == 'cmp')) {
            $gmt = $this->setGmt(false);
        } else {
            $gmt = $this->setGmt(true);
        }
        if ($time instanceof Zend_Date) {
            // extract time from object
            $time = $time->get(Zend_Date::TIME_MEDIUM, $locale);
        } else {
            $parsed = Zend_Locale_Format::getTime($time, $format, $locale);
            $time = new Zend_Date(0, Zend_Date::TIMESTAMP, $locale);
            $time->setGmt(true);
            $time->set($parsed['hour'],   Zend_Date::HOUR);
            $time->set($parsed['minute'], Zend_Date::MINUTE);
            $time->set($parsed['second'], Zend_Date::SECOND);
            $time = $time->get(Zend_Date::TIME_MEDIUM, $locale);
        }

        $return = $this->_calcdetail($calc, $time, Zend_Date::TIME_MEDIUM, $locale);
        $this->setGmt($gmt);
        if ($calc != 'cmp') {
            return $this;
        }
        return $return;
    }


    /**
     * Sets a new time for the date object. Format defines how to parse the time string.
     * Also a complete date can be given, but only the time is used for setting.
     * For example: dd.MMMM.yyTHH:mm' and 'ss sec'-> 10.May.07T25:11 and 44 sec => 1h11min44sec + 1 day
     * Returned is the new date object and the existing date is left as it was before
     *
     * @param  string|integer|Zend_Date  $time    Time to set
     * @param  string                    $format  OPTIONAL Timeformat for parsing input
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new time
     * @throws Zend_Date_Exception
     */
    public function setTime($time, $format = null, $locale = null)
    {
        return $this->_time('set', $time, $format, $locale);
    }


    /**
     * Adds a time to the existing date. Format defines how to parse the time string.
     * If only parts are given the other parts are set to 0.
     * If no format is given, the standardformat of this locale is used.
     * For example: HH:mm:ss -> 10 -> +10 hours
     *
     * @param  string|integer|Zend_Date  $time    Time to add
     * @param  string                    $format  OPTIONAL Timeformat for parsing input
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new time
     * @throws Zend_Date_Exception
     */
    public function addTime($time, $format = null, $locale = null)
    {
        return $this->_time('add', $time, $format, $locale);
    }


    /**
     * Subtracts a time from the existing date. Format defines how to parse the time string.
     * If only parts are given the other parts are set to 0.
     * If no format is given, the standardformat of this locale is used.
     * For example: HH:mm:ss -> 10 -> -10 hours
     *
     * @param  string|integer|Zend_Date  $time    Time to sub
     * @param  string                    $format  OPTIONAL Timeformat for parsing input
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new time
     * @throws Zend_Date_Exception
     */
    public function subTime($time, $format = null, $locale = null)
    {
        return $this->_time('sub', $time, $format, $locale);
    }


    /**
     * Compares the time from the existing date. Format defines how to parse the time string.
     * If only parts are given the other parts are set to default.
     * If no format us given, the standardformat of this locale is used.
     * For example: HH:mm:ss -> 10 -> 10 hours
     *
     * @param  string|integer|Zend_Date  $time    Time to compare
     * @param  string                    $format  OPTIONAL Timeformat for parsing input
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareTime($time, $format = null, $locale = null)
    {
        return $this->_time('cmp', $time, $format, $locale);
    }


    /**
     * Returns a clone of $this, with the time part set to 00:00:00.
     *
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getDate($locale = null)
    {
        return $this->copyPart(Zend_Date::DATE_MEDIUM, $locale);
    }


    /**
     * Returns the calculated date
     *
     * @param  string                    $calc    Calculation to make
     * @param  string|integer|Zend_Date  $date    Date to calculate with, if null the actual date is taken
     * @param  string                    $format  Dateformat for parsing
     * @param  string|Zend_Locale        $locale  Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    private function _date($calc, $date, $format, $locale)
    {
        if (is_null($date)) {
            throw new Zend_Date_Exception('parameter $date must be set, null is not allowed');
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }

        if (($calc == 'set') or ($calc == 'cmp')) {
            $gmt = $this->setGmt(false);
        } else {
            $gmt = $this->setGmt(true);
        }
        if ($date instanceof Zend_Date) {
            // extract date from object
            $date = $date->get(Zend_Date::DATE_MEDIUM, $locale);
        } else {
            $parsed = Zend_Locale_Format::getDate($date, $format, $locale);
            $date = new Zend_Date(0, Zend_Date::TIMESTAMP, $locale);
            $date->setGmt(true);
            $date->set($parsed['year'], Zend_Date::YEAR);
            $date->set($parsed['month'], Zend_Date::MONTH_SHORT);
            $date->set($parsed['day'], Zend_Date::DAY);
            $date = $date->get(Zend_Date::DATE_MEDIUM, $locale);
        }

        $return = $this->_calcdetail($calc, $date, Zend_Date::DATE_MEDIUM, $locale);
        $this->setGmt($gmt);
        if ($calc != 'cmp') {
            return $this;
        }
        return $return;
    }


    /**
     * Sets a new date for the date object. Format defines how to parse the date string.
     * Also a complete date with time can be given, but only the date is used for setting.
     * For example: MMMM.yy HH:mm-> May.07 22:11 => 01.May.07 00:00
     * Returned is the new date object and the existing time is left as it was before
     *
     * @param  string|integer|Zend_Date  $time    Date to set
     * @param  string                    $format  OPTIONAL Dateformat for parsing
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function setDate($date, $format = null, $locale = null)
    {
        return $this->_date('set', $date, $format, $locale);
    }


    /**
     * Adds a date to the existing date object. Format defines how to parse the date string.
     * If only parts are given the other parts are set to 0.
     * If no format is given, the standardformat of this locale is used.
     * For example: MM.dd.YYYY -> 10 -> +10 months
     *
     * @param  string|integer|Zend_Date  $time    Date to add
     * @param  string                    $format  OPTIONAL Dateformat for parsing input
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function addDate($date, $format = null, $locale = null)
    {
        return $this->_date('add', $date, $format, $locale);
    }


    /**
     * Subtracts a date from the existing date object. Format defines how to parse the date string.
     * If only parts are given the other parts are set to 0.
     * If no format is given, the standardformat of this locale is used.
     * For example: MM.dd.YYYY -> 10 -> -10 months
     * Be aware: Subtracting 2 months is not equal to Adding -2 months !!!
     *
     * @param  string|integer|Zend_Date  $time    Date to sub
     * @param  string                    $format  OPTIONAL Dateformat for parsing input
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function subDate($date, $format = null, $locale = null)
    {
        return $this->_date('sub', $date, $format, $locale);
    }


    /**
     * Compares the date from the existing date object, ignoring the time. 
     * Format defines how to parse the date string.
     * If only parts are given the other parts are set to 0.
     * If no format is given, the standardformat of this locale is used.
     * For example: 10.01.2000 => 10.02.1999 -> false
     *
     * @param  string|integer|Zend_Date  $time    Date to compare
     * @param  string                    $format  OPTIONAL Dateformat for parsing input
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function compareDate($date, $format = null, $locale = null)
    {

        return $this->_date('cmp', $date, $format, $locale);
    }


    /**
     * Returns the full ISO 8601 date from the date object.
     * Always the complete ISO 8601 specifiction is used. If an other ISO date is needed
     * (ISO 8601 defines several formats) use toString() instead. 
     * This function does not return the ISO date as object. Use copy() instead. 
     *
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return string
     */
    public function getIso($locale = null)
    {
        return $this->get(Zend_Date::ISO_8601, $locale);
    }


    /**
     * Returns the calculated ISO date
     *
     * @param  string                    $calc    Calculation to make
     * @param  string|integer|Zend_Date  $iso     ISO date to calculate with, if null the actual date is taken
     * @param  string|Zend_Locale        $locale  Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    private function _iso($calc, $iso, $locale)
    {
        if (is_null($iso)) {
            throw new Zend_Date_Exception('parameter $date must be set, null is not allowed');
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }

        $this->_setGmt(false);
        if ($iso instanceof Zend_Date) {
            // extract iso from object
            $iso = $iso->get(Zend_Date::ISO_8601, $locale);
        }

        $return = $this->_calcdetail($calc, $iso, Zend_Date::ISO_8601, $locale);
        $this->_resetGmt();
        if ($calc != 'cmp') {
            return $this;
        }
        return $return;
    }


    /**
     * Sets a new date for the date object. Not given parts are set to default.
     * Only supported ISO 8601 formats are accepted.
     * For example: 050901 -> 01.Sept.2005 00:00:00, 20050201T10:00:30 -> 01.Feb.2005 10h00m30s
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $date    ISO Date to set
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function setIso($date, $locale = null)
    {
        return $this->_iso('set', $date, $locale);
    }


    /**
     * Adds a ISO date to the date object. Not given parts are set to default.
     * Only supported ISO 8601 formats are accepted.
     * For example: 050901 -> + 01.Sept.2005 00:00:00, 10:00:00 -> +10h
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $date    ISO Date to add
     * @param  boolean                   $gmt     OPTIONAL true = UTC time, false = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function addIso($date, $locale = null)
    {
        return $this->_iso('add', $date, $locale);
    }


    /**
     * Subtracts a ISO date from the date object. Not given parts are set to default.
     * Only supported ISO 8601 formats are accepted.
     * For example: 050901 -> - 01.Sept.2005 00:00:00, 10:00:00 -> -10h
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $date    ISO Date to sub
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function subIso($date, $locale = null)
    {
        return $this->_iso('sub', $date, $locale);
    }


    /**
     * Compares a ISO date with the date object. Not given parts are set to default.
     * Only supported ISO 8601 formats are accepted.
     * For example: 050901 -> - 01.Sept.2005 00:00:00, 10:00:00 -> -10h
     * Returns if equal, earlier or later
     *
     * @param  string|integer|Zend_Date  $date    ISO Date to sub
     * @param  boolean                   $gmt     OPTIONAL true = UTC time, false = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareIso($date, $locale = null)
    {
        return $this->_iso('cmp', $date, $locale);
    }


    /**
     * Returns a RFC 822 compilant datestring from the date object.
     * This function does not return the RFC date as object. Use copy() instead. 
     *
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return string
     */
    public function getArpa($locale = null)
    {
        return $this->get(Zend_Date::RFC_822, $locale);
    }


    /**
     * Returns the calculated RFC 822 date
     *
     * @param  string                    $calc    Calculation to make
     * @param  string|integer|Zend_Date  $arpa    RFC 822 date to calculate with, if null the actual date is taken
     * @param  string|Zend_Locale        $locale  Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    private function _arpa($calc, $arpa, $locale)
    {
        if (is_null($arpa)) {
            throw new Zend_Date_Exception('parameter $date must be set, null is not allowed');
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }

        if (($calc == 'set') or ($calc == 'cmp')) {
            $gmt = $this->setGmt(false);
        } else {
            $gmt = $this->setGmt(true);
        }
        if ($arpa instanceof Zend_Date) {
            // extract arpa fromobject
            $arpa = $arpa->get(Zend_Date::RFC_822, $locale);
        }

        $return = $this->_calcdetail($calc, $arpa, Zend_Date::RFC_822, $locale);
        $this->setGmt($gmt);
        if ($calc != 'cmp') {
            return $this;
        }
        return $return;
    }


    /**
     * Sets a RFC 822 date as new date for the date object.
     * Only RFC 822 compilant date strings are accepted.
     * For example: Sat, 14 Feb 09 00:31:30 +0100
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $date    RFC 822 to set
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function setArpa($date, $locale = null)
    {
        return $this->_arpa('set', $date, $locale);
    }


    /**
     * Adds a RFC 822 date to the date object.
     * ARPA messages are used in emails or HTTP Headers.
     * Only RFC 822 compilant date strings are accepted.
     * For example: Sat, 14 Feb 09 00:31:30 +0100
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $date    RFC 822 Date to add
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function addArpa($date, $locale = null)
    {
        return $this->_arpa('add', $date, $locale);
    }


    /**
     * Subtracts a RFC 822 date from the date object.
     * ARPA messages are used in emails or HTTP Headers.
     * Only RFC 822 compilant date strings are accepted.
     * For example: Sat, 14 Feb 09 00:31:30 +0100
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $date    RFC 822 Date to sub
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function subArpa($date, $locale = null)
    {
        return $this->_arpa('sub', $date, $locale);
    }


    /**
     * Compares a RFC 822 compilant date with the date object.
     * ARPA messages are used in emails or HTTP Headers.
     * Only RFC 822 compilant date strings are accepted.
     * For example: Sat, 14 Feb 09 00:31:30 +0100
     * Returns if equal, earlier or later
     *
     * @param  string|integer|Zend_Date  $date    RFC 822 Date to sub
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareArpa($date, $locale = null)
    {
        return $this->_arpa('cmp', $date, $locale);
    }


    /**
     * Check if location is supported
     *
     * @param $location array - locations array
     * @return $horizon float
     */
    private function _checkLocation($location)
    {
        if (!isset($location['longitude']) or !isset($location['latitude'])) {
            throw new Zend_Date_Exception('Location must include \'longitude\' and \'latitude\'', $location);
        }
        if (($location['longitude'] > 180) or ($location['longitude'] < -180)) {
            throw new Zend_Date_Exception('Longitude must be between -180 and 180', $location);
        }
        if (($location['latitude'] > 90) or ($location['latitude'] < -90)) {
            throw new Zend_Date_Exception('Latitude must be between -90 and 90', $location);
        }

        if (!isset($location['horizon'])){
            $location['horizon'] = 'effective';
        }

        switch ($location['horizon']) {
            case 'civil' :
                return -0.104528;
                break;
            case 'nautic' :
                return -0.207912;
                break;
            case 'astronomic' :
                return -0.309017;
                break;
            default :
                return -0.0145439;
                break;
        }
    }


    /**
     * Returns the time of sunrise for this date and a given location as new date object
     * For a list of cities and correct locations use the class Zend_Date_Cities
     *
     * @param  $location array - location of sunrise
     *                   ['horizon']   -> civil, nautic, astronomical, effective (default)
     *                   ['longitude'] -> longitude of location
     *                   ['latitude']  -> latitude of location
     * @return Zend_Date
     * @throws Zend_Date_Exception
     */
    public function getSunRise($location)
    {
        $horizon = $this->_checkLocation($location);
        $result = clone $this;
        $result->set($this->calcSun($location, $horizon, true), 'Zend_Date::TIMESTAMP');
        return $result;
    }


    /**
     * Returns the time of sunset for this date and a given location as new date object
     * For a list of cities and correct locations use the class Zend_Date_Cities
     *
     * @param  $location array - location of sunset
     *                   ['horizon']   -> civil, nautic, astronomical, effective (default)
     *                   ['longitude'] -> longitude of location
     *                   ['latitude']  -> latitude of location
     * @return Zend_Date
     * @throws Zend_Date_Exception
     */
    public function getSunSet($location)
    {
        $horizon = $this->_checkLocation($location);
        $result = clone $this;
        $result->set($this->calcSun($location, $horizon, false), 'Zend_Date::TIMESTAMP');
        return $result;
    }


    /**
     * Returns an array with the sunset and sunrise dates for all horizon types
     * For a list of cities and correct locations use the class Zend_Date_Cities
     *
     * @param  $location array - location of suninfo
     *                   ['horizon']   -> civil, nautic, astronomical, effective (default)
     *                   ['longitude'] -> longitude of location
     *                   ['latitude']  -> latitude of location
     * @return array - [sunset|sunrise][effective|civil|nautic|astronomic]
     * @throws Zend_Date_Exception
     */
    public function getSunInfo($location)
    {
        $suninfo = array();
        for ($i = 0; $i < 4; ++$i) {
            switch ($i) {
                case 0 :
                    $location['horizon'] = 'effective';
                    break;
                case 1 :
                    $location['horizon'] = 'civil';
                    break;
                case 2 :
                    $location['horizon'] = 'nautic';
                    break;
                case 3 :
                    $location['horizon'] = 'astronomic';
                    break;
            }
            $horizon = $this->_checkLocation($location);
            $result = clone $this;
            $result->set($this->calcSun($location, $horizon, true), 'Zend_Date::TIMESTAMP');
            $suninfo['sunrise'][$location['horizon']] = $result;
            $result = clone $this;
            $result->set($this->calcSun($location, $horizon, false), 'Zend_Date::TIMESTAMP');
            $suninfo['sunset'][$location['horizon']]  = $result;
        }
        return $suninfo;
    }


    /**
     * Check a given year for leap year.
     *
     * @param  integer|Zend_Date  $year  Year to check
     * @return boolean
     */
    public static function checkLeapYear($year)
    {
        if ($year instanceof Zend_Date) {
            $year = (int) $year->get(Zend_Date::YEAR);
        }
        if (!is_numeric($year)) {
            throw new Zend_Date_Exception("year ($year) has to be integer for isLeapYear()", $year);
        }
        
        return (bool) parent::isYearLeapYear($year);
    }


    /**
     * Returns true, if the year is a leap year.
     *
     * @return boolean
     */
    public function isLeapYear()
    {
        return self::checkLeapYear($this);
    }


    /**
     * Returns if the set date is todays date
     *
     * @return boolean
     */
    public function isToday()
    {
        $today = $this->date('Ymd');
        $day   = $this->date('Ymd', $this->getUnixTimestamp());
        return ($today == $day);
    }


    /**
     * Returns if the set date is yesterdays date
     *
     * @return boolean
     */
    public function isYesterday()
    {
        $today = $this->date('Ymd');
        $day   = $this->date('Ymd',$this->getUnixTimestamp());
        return (((int) $today - (int) $day) == 1);
    }


    /**
     * Returns if the set date is tomorrows date
     *
     * @return boolean
     */
    public function isTomorrow()
    {
        $today = $this->date('Ymd');
        $day   = $this->date('Ymd',$this->getUnixTimestamp());
        return (($today - $day) == -1);
    }


    /**
     * Returns the actual date as new date object
     *
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public static function now($locale = null)
    {
        return new Zend_Date(time(), Zend_Date::TIMESTAMP, $locale);
    }


    /**
     * Calculate date details
     * 
     * @param  string                    $calc    Calculation to make
     * @param  string|integer|Zend_Date  $date    Date or Part to calculate
     * @param  string                    $part    Datepart for Calculation
     * @param  boolean                   $gmt     true = UTC time, false = actual time zone
     * @param  string|Zend_Locale        $locale  Locale for parsing input
     * @return integer|string  new date
     * @throws Zend_Date_Exception
     */
    private function _calcdetail($calc, $date, $type, $locale)
    {
        switch($calc) {
            case 'set' :
                return $this->set($date, $type, $locale);
                break;
            case 'add' :
                return $this->add($date, $type, $locale);
                break;
            case 'sub' :
                return $this->sub($date, $type, $locale);
                break;
        }
        return $this->compare($date, $type, $locale);
    }

    /**
     * Returns only the year from the date object as new object.
     * For example: 10.May.2000 10:30:00 -> 01.Jan.2000 00:00:00
     *
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getYear($locale = null)
    {
        return $this->copyPart(Zend_Date::YEAR, $locale);
    }

    /**
     * Returns the calculated date
     *
     * @param  string                    $calc    Calculation to make
     * @param  string|integer|Zend_Date  $year    Year to calculate with, if null the actual year is taken
     * @param  string|Zend_Locale        $locale  Locale for parsing input
     * @return integer|Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    private function _year($calc, $year, $locale)
    {
        if (is_null($year)) {
            throw new Zend_Date_Exception('parameter $year must be set, null is not allowed');
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }

        $this->_setGmt(false);
        if ($year instanceof Zend_Date) {
            // extract year from object
            $year = $year->get(Zend_Date::YEAR, $locale);
        } else if (!is_numeric($year)) {
            throw new Zend_Date_Exception("invalid year ($year) operand", $year);
        }

        $return = $this->_calcdetail($calc, $year, Zend_Date::YEAR, $locale);
        $this->_resetGmt();
        if ($calc != 'cmp') {
            return $this;
        }
        return $return;
    }


    /**
     * Sets a new year
     * If the year is between 0 and 69, 2000 will be set (2000-2069)
     * If the year if between 70 and 99, 1999 will be set (1970-1999)
     * 3 or 4 digit years are set as expected. If you need to set year 0-99
     * use set() instead. 
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $date    Year to set
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function setYear($year, $locale = null)
    {
        return $this->_year('set', $year, $locale);
    }


    /**
     * Adds the year to the existing date object
     * If the year is between 0 and 69, 2000 will be added (2000-2069)
     * If the year if between 70 and 99, 1999 will be added (1970-1999)
     * 3 or 4 digit years are added as expected. If you need to add years from 0-99
     * use add() instead. 
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $date    Year to add
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function addYear($year, $locale = null)
    {
        return $this->_year('add', $year, $locale);
    }


    /**
     * Subs the year from the existing date object
     * If the year is between 0 and 69, 2000 will be subtracted (2000-2069)
     * If the year if between 70 and 99, 1999 will be subtracted (1970-1999)
     * 3 or 4 digit years are subtracted as expected. If you need to subtract years from 0-99
     * use sub() instead. 
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $date    Year to sub
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function subYear($year, $locale = null)
    {
        return $this->_year('sub', $year, $locale);
    }


    /**
     * Compares the year with the existing date object, ignoring other date parts. 
     * For example: 10.03.2000 -> 15.02.2000 -> true
     * Returns if equal, earlier or later
     *
     * @param  string|integer|Zend_Date  $year    Year to compare
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareYear($year, $locale = null)
    {
        return $this->_year('cmp', $year, $locale);
    }


    /**
     * Returns only the month from the date object as new object.
     * For example: 10.May.2000 10:30:00 -> 01.May.1970 00:00:00
     *
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getMonth($locale = null)
    {
        return $this->copyPart(Zend_Date::MONTH_SHORT, $locale);
    }


    /**
     * Returns the calculated month
     *
     * @param  string                    $calc    Calculation to make
     * @param  string|integer|Zend_Date  $month   Month to calculate with, if null the actual month is taken
     * @param  string|Zend_Locale        $locale  Locale for parsing input
     * @return integer|Zend_Date  new time
     * @throws Zend_Date_Exception
     */
    private function _month($calc, $month, $locale)
    {
        if (is_null($month)) {
            throw new Zend_Date_Exception('parameter $month must be set, null is not allowed');
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }

        $this->_setGmt(false);
        if ($month instanceof Zend_Date) {
            // extract month from object
            $found = $month->get(Zend_Date::MONTH_DIGIT, $locale);
        } else {
            if (is_numeric($month)) {
                $found = $month;
            } else {
                $monthlist = Zend_Locale_Data::getContent($locale, 'monthlist', array('gregorian', 'wide'));
                $monthlist2 = Zend_Locale_Data::getContent($locale, 'monthlist', array('gregorian', 'abbreviated'));

                $monthlist = array_merge($monthlist, $monthlist2);
                $found = 0;
                $cnt = 0;
                foreach ($monthlist as $key => $value) {
                    if (strtoupper($value) == strtoupper($month)) {
                        $found = $key + 1;
                        break;
                    }
                    ++$cnt;
                }
                if ($found == 0) {
                    foreach ($monthlist2 as $key => $value) {
                        if (strtoupper(substr($value, 0, 1)) == strtoupper($month)) {
                            $found = $key + 1;
                            break;
                        }
                        ++$cnt;
                    }
                }
                if ($found == 0) {
                    throw new Zend_Date_Exception("unknown month name ($month)", $month);
                }
            }
        }

        $return = $this->_calcdetail($calc, $found, Zend_Date::MONTH_DIGIT, $locale);
        if ($calc != 'cmp') {
            return $this;
        }
        return $return;
    }


    /**
     * Sets a new month
     * The month can be a number or a string. Setting months lower then 0 and greater then 12
     * will result in adding or subtracting the relevant year. (12 months equal one year)
     * If a localized monthname is given it will be parsed with the default locale or the optional
     * set locale.
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $month   Month to set
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function setMonth($month, $locale = null)
    {
        return $this->_month('set', $month, $locale);
    }


    /**
     * Adds months to the existing date object. 
     * The month can be a number or a string. Adding months lower then 0 and greater then 12
     * will result in adding or subtracting the relevant year. (12 months equal one year)
     * If a localized monthname is given it will be parsed with the default locale or the optional
     * set locale.
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $month   Month to add
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function addMonth($month, $locale = null)
    {
        return $this->_month('add', $month, $locale);
    }


    /**
     * Subtracts months from the existing date object. 
     * The month can be a number or a string. Subtracting months lower then 0 and greater then 12
     * will result in adding or subtracting the relevant year. (12 months equal one year)
     * If a localized monthname is given it will be parsed with the default locale or the optional
     * set locale.
     * Returned is the new date object
     *
     * @param  string|integer|Zend_Date  $month   Month to sub
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function subMonth($month, $locale = null)
    {
        return $this->_month('sub', $month, $locale);
    }


    /**
     * Compares the month with the existing date object, ignoring other date parts. 
     * For example: 10.03.2000 -> 15.03.1950 -> true
     * Returns if equal, earlier or later
     *
     * @param  string|integer|Zend_Date  $month   Month to compare
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareMonth($month, $locale = null)
    {
        return $this->_month('cmp', $month, $locale);
    }


    /**
     * Returns the day as new date object
     * Example: 20.May.1986 -> 20.Jan.1970 00:00:00
     *
     * @param $locale  string|Zend_Locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getDay($locale = null)
    {
        return $this->copyPart(Zend_Date::DAY_SHORT, $locale);
    }


    /**
     * Returns the calculated day
     *
     * @param $calc    string                    Type of calculation to make
     * @param $day     string|integer|Zend_Date  Day to calculate, when null the actual day is calculated
     * @param $locale  string|Zend_Locale        Locale for parsing input
     * @return Zend_Date|integer
     */
    private function _day($calc, $day, $locale)
    {
        if (is_null($day)) {
            throw new Zend_Date_Exception('parameter $day must be set, null is not allowed');
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }

        $gmt = $this->setGmt(true);
        if ($day instanceof Zend_Date) {
            $day = $day->get(Zend_Date::DAY_SHORT, $locale);
        }

        if (is_numeric($day)) {
            $type = Zend_Date::DAY_SHORT;
        } else {
            switch (strlen($day)) {
                case 1 :
                   $type = Zend_Date::WEEKDAY_NARROW;
                    break;
                case 2:
                    $type = Zend_Date::WEEKDAY_NAME;
                    break;
                case 3:
                    $type = Zend_Date::WEEKDAY_SHORT;
                    break;
                default:
                    $type = Zend_Date::WEEKDAY;
                    break;
            }
        }
        $return = $this->_calcdetail($calc, $day, $type, $locale);
        $this->setGmt($gmt);
        if ($calc != 'cmp') {
            return $this;
        }
        return $return;
    }


    /**
     * Sets a new day
     * The day can be a number or a string. Setting days lower then 0 or greater than the number of this months days
     * will result in adding or subtracting the relevant month.
     * If a localized dayname is given it will be parsed with the default locale or the optional
     * set locale.
     * Returned is the new date object
     * Example: setDay('Montag', 'de_AT'); will set the monday of this week as day.
     *
     * @param  string|integer|Zend_Date  $month   Day to set
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function setDay($day, $locale = null)
    {
        return $this->_day('set', $day, $locale);
    }


    /**
     * Adds days to the existing date object. 
     * The day can be a number or a string. Adding days lower then 0 or greater than the number of this months days
     * will result in adding or subtracting the relevant month.
     * If a localized dayname is given it will be parsed with the default locale or the optional
     * set locale.
     * Returned is the new date object
     * Example: addDay('Montag', 'de_AT'); will add the number of days until the next monday
     *
     * @param  string|integer|Zend_Date  $month   Day to add
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function addDay($day, $locale = null)
    {
        return $this->_day('add', $day, $locale);
    }


    /**
     * Subtracts days from the existing date object. 
     * The day can be a number or a string. Subtracting days lower then 0 or greater than the number of this months days
     * will result in adding or subtracting the relevant month.
     * If a localized dayname is given it will be parsed with the default locale or the optional
     * set locale.
     * Returned is the new date object
     * Example: subDay('Montag', 'de_AT'); will sub the number of days until the previous monday
     *
     * @param  string|integer|Zend_Date  $month   Day to sub
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function subDay($day, $locale = null)
    {
        return $this->_day('sub', $day, $locale);
    }


    /**
     * Compares the day with the existing date object, ignoring other date parts. 
     * For example: 'Monday', 'en' -> 08.Jan.2007 -> 0
     * Returns if equal, earlier or later
     *
     * @param  string|integer|Zend_Date  $day     Day to compare
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareDay($day, $locale = null)
    {
        return $this->_day('cmp', $day, $locale);
    }


    /**
     * Returns the weekday as new date object
     * Weekday is always from 1-7
     * Example: 09-Jan-2007 -> 2 = Tuesday -> 02-Jan-1970 (when 02.01.1970 is also Tuesday)
     *
     * @param $locale  string|Zend_Locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getWeekday($locale = null)
    {
        return $this->copyPart(Zend_Date::WEEKDAY, $locale);
    }


    /**
     * Returns the calculated weekday
     *
     * @param  $calc     string                    Type of calculation to make
     * @param  $weekday  string|integer|Zend_Date  Weekday to calculate, when null the actual weekday is calculated
     * @param  $locale   string|Zend_Locale        Locale for parsing input
     * @return Zend_Date|integer
     * @throws Zend_Date_Exception
     */
    private function _weekday($calc, $weekday, $locale)
    {
        if (is_null($weekday)) {
            throw new Zend_Date_Exception('parameter $weekday must be set, null is not allowed');
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }

        $this->_setGmt(false);
        if ($weekday instanceof Zend_Date) {
            $weekday = $weekday->get(Zend_Date::WEEKDAY_DIGIT, $locale);
        }

        if (is_numeric($weekday)) {
            $type = Zend_Date::WEEKDAY_DIGIT;
        } else {
            switch(strlen($weekday)) {
                case 1:
                   $type = Zend_Date::WEEKDAY_NARROW;
                    break;
                case 2:
                    $type = Zend_Date::WEEKDAY_NAME;
                    break;
                case 3:
                    $type = Zend_Date::WEEKDAY_SHORT;
                    break;
                default:
                    $type = Zend_Date::WEEKDAY;
                    break;
            }
        }
        $return = $this->_calcdetail($calc, $weekday, $type, $locale);
        $this->_resetGmt();
        if ($calc != 'cmp') {
            return $this;
        }
        return $return;
    }


    /**
     * Sets a new weekday
     * The weekday can be a number or a string. Specifying a weekday integer lower then 1,
     * or greater than 7 will result in adding or subtracting the relevant number of weekdays
     * to the current date of $this.  If a localized weekday name is given, then it will be
     * parsed as a date in $locale (defaults to the same locale as $this).
     * Returned is the new date object.
     * Example: setWeekday(3); will set the wednesday of this week as day.
     *
     * @param  string|integer|Zend_Date  $month   Weekday to set
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function setWeekday($weekday, $locale = null)
    {
        return $this->_weekday('set', $weekday, $locale);
    }


    /**
     * Adds weekdays to the existing date object.
     * The weekday can be a number or a string. Adding days lower then 1 or greater than 7
     * will result in adding or subtracting the relevant number of weeks.
     * If a localized dayname is given it will be parsed with the default locale or the optional
     * set locale.
     * Returned is the new date object
     * Example: addWeekday(3); will add the difference of days from the begining of the month until 
     * wednesday.
     *
     * @param  string|integer|Zend_Date  $month   Weekday to add
     * @param  boolean                   $gmt     OPTIONAL true = UTC time, false = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function addWeekday($weekday, $locale = null)
    {
        return $this->_weekday('add', $weekday, $locale);
    }


    /**
     * Subtracts weekdays from the existing date object.
     * The weekday can be a number or a string. Subtracting days lower then 1 or greater than 7
     * will result in adding or subtracting the relevant number of weeks.
     * If a localized dayname is given it will be parsed with the default locale or the optional
     * set locale.
     * Returned is the new date object
     * Example: subWeekday(3); will subtract the difference of days from the begining of the month until 
     * wednesday.
     *
     * @param  string|integer|Zend_Date  $month   Weekday to sub
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function subWeekday($weekday, $locale = null)
    {
        return $this->_weekday('sub', $weekday, $locale);
    }


    /**
     * Compares the weekday with the existing date object, ignoring other date parts. 
     * For example: 'Monday', 'en' -> 08.Jan.2007 -> 0
     * Returns if equal, earlier or later
     *
     * @param  string|integer|Zend_Date  $weekday  Weekday to compare
     * @param  string|Zend_Locale        $locale   OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareWeekday($weekday, $locale = null)
    {
        return $this->_weekday('cmp', $weekday, $locale);
    }


    /**
     * Returns the day of year as new date object
     * Example: 02.Feb.1986 10:00:00 -> 02.Feb.1970 00:00:00
     *
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getDayOfYear($locale = null)
    {
        return $this->copyPart(Zend_Date::DAY_OF_YEAR, $locale);
    }


    /**
     * Returns the calculated day of the year
     *
     * @param  string                    $calc   Type of calculation to make
     * @param  string|integer|Zend_Date  $day    Day of year to calculate, when null the actual day of year is calculated
     * @param  string|Zend_Locale        $locale Locale for parsing input
     * @return Zend_Date|integer
     * @throws Zend_Date_Exception
     */
    private function _dayOfYear($calc, $day, $locale)
    {
        if (is_null($day)) {
            throw new Zend_Date_Exception('parameter $day must be set, null is not allowed');
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }

        $this->_setGmt(false);
        if ($day instanceof Zend_Date) {
            $day = $day->get(Zend_Date::DAY_OF_YEAR, $locale);
        }

        $return = $this->_calcdetail($calc, $day, Zend_Date::DAY_OF_YEAR, $locale);
        $this->_resetGmt();
        if ($calc != 'cmp') {
            return $this;
        }
        return $return;
    }


    /**
     * Sets a new day of year
     * The day of year is always a number.
     * Returned is the new date object
     * Example: 04.May.2004 -> setDayOfYear(10) -> 10.Jan.2004
     *
     * @param  string|integer|Zend_Date  $day     Day of Year to set
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function setDayOfYear($day, $locale = null)
    {
        return $this->_dayOfYear('set', $day, $locale);
    }


    /**
     * Adds a day of year to the existing date object.
     * The day of year is always a number.
     * Returned is the new date object
     * Example: addDayOfYear(10); will add 10 days to the existing date object.
     *
     * @param  string|integer|Zend_Date  $day     Day of Year to add
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function addDayOfYear($day, $locale = null)
    {
        return $this->_dayOfYear('add', $day, $locale);
    }


    /**
     * Subtracts a day of year from the existing date object.
     * The day of year is always a number.
     * Returned is the new date object
     * Example: subDayOfYear(10); will subtract 10 days from the existing date object.
     *
     * @param  string|integer|Zend_Date  $day     Day of Year to sub
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function subDayOfYear($day, $locale = null)
    {
        return $this->_dayOfYear('sub', $day, $locale);
    }


    /**
     * Compares the day of year with the existing date object. 
     * For example: compareDayOfYear(33) -> 02.Feb.2007 -> 0
     * Returns if equal, earlier or later
     *
     * @param  string|integer|Zend_Date  $day     Day of Year to compare
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareDayOfYear($day, $locale = null)
    {
        return $this->_dayOfYear('cmp', $day, $locale);
    }


    /**
     * Returns the hour as new date object
     * Example: 02.Feb.1986 10:30:25 -> 01.Jan.1970 10:00:00
     *
     * @param $locale  string|Zend_Locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getHour($locale = null)
    {
        return $this->copyPart(Zend_Date::HOUR, $locale);
    }


    /**
     * Returns the calculated hour
     *
     * @param  string                    $calc    Type of calculation to make
     * @param  string|integer|Zend_Date  $hour    Hour to calculate, when null the actual hour is calculated
     * @param  string|Zend_Locale        $locale  Locale for parsing input
     * @return Zend_Date|integer
     * @throws Zend_Date_Exception
     */
    private function _hour($calc, $hour, $locale)
    {
        if (is_null($hour)) {
            throw new Zend_Date_Exception('parameter $hour must be set, null is not allowed');
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }

        $this->_setGmt(false);
        if ($hour instanceof Zend_Date) {
            $hour = $hour->get(Zend_Date::HOUR_SHORT, $locale);
        } else if (!is_numeric($hour)) {
            throw new Zend_Date_Exception("invalid hour ($hour) operand", $hour);
        }

        $return = $this->_calcdetail($calc, $hour, Zend_Date::HOUR_SHORT, $locale);
        if ($calc != 'cmp') {
            return $this;
        }
        return $return;
    }


    /**
     * Sets a new hour
     * The hour is always a number.
     * Returned is the new date object
     * Example: 04.May.1993 13:07:25 -> setHour(7); -> 04.May.1993 07:07:25
     *
     * @param  string|integer|Zend_Date  $hour    Hour to set
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function setHour($hour, $locale = null)
    {
        return $this->_hour('set', $hour, $locale);
    }


    /**
     * Adds hours to the existing date object.
     * The hour is always a number.
     * Returned is the new date object
     * Example: 04.May.1993 13:07:25 -> addHour(12); -> 05.May.1993 01:07:25
     *
     * @param  string|integer|Zend_Date  $hour    Hour to add
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function addHour($hour, $locale = null)
    {
        return $this->_hour('add', $hour, $locale);
    }


    /**
     * Subtracts hours from the existing date object.
     * The hour is always a number.
     * Returned is the new date object
     * Example: 04.May.1993 13:07:25 -> subHour(6); -> 05.May.1993 07:07:25
     *
     * @param  string|integer|Zend_Date  $hour    Hour to sub
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function subHour($hour, $locale = null)
    {
        return $this->_hour('sub', $hour, $locale);
    }


    /**
     * Compares the hour with the existing date object. 
     * For example: 10:30:25 -> compareHour(10) -> 0
     * Returns if equal, earlier or later
     *
     * @param  string|integer|Zend_Date  $hour    Hour to compare
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareHour($hour, $locale = null)
    {
        return $this->_hour('cmp', $hour, $locale);
    }


    /**
     * Returns the minute as new date object
     * Example: 02.Feb.1986 10:30:25 -> 01.Jan.1970 00:30:00
     *
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getMinute($locale = null)
    {
        return $this->copyPart(Zend_Date::MINUTE, $locale);
    }


    /**
     * Returns the calculated minute
     *
     * @param  string                    $calc    Type of calculation to make
     * @param  string|integer|Zend_Date  $minute  Minute to calculate, when null the actual minute is calculated
     * @param  string|Zend_Locale        $locale  Locale for parsing input
     * @return Zend_Date|integer
     * @throws Zend_Date_Exception
     */
    private function _minute($calc, $minute, $locale)
    {
        if (is_null($minute)) {
            throw new Zend_Date_Exception('parameter $minute must be set, null is not allowed');
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }

        $this->_setGmt(false);
        if ($minute instanceof Zend_Date) {
            $minute = $minute->get(Zend_Date::MINUTE_SHORT, $locale);
        } else if (!is_numeric($minute)) {
            throw new Zend_Date_Exception("invalid minute ($minute) operand", $minute);
        }

        $return = $this->_calcdetail($calc, $minute, Zend_Date::MINUTE_SHORT, $locale);
        if ($calc != 'cmp') {
            return $this;
        }
        return $return;
    }


    /**
     * Sets a new minute
     * The minute is always a number.
     * Returned is the new date object
     * Example: 04.May.1993 13:07:25 -> setMinute(29); -> 04.May.1993 13:29:25
     *
     * @param  string|integer|Zend_Date  $minute  Minute to set
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function setMinute($minute, $locale = null)
    {
        return $this->_minute('set', $minute, $locale);
    }


    /**
     * Adds minutes to the existing date object.
     * The minute is always a number.
     * Returned is the new date object
     * Example: 04.May.1993 13:07:25 -> addMinute(65); -> 04.May.1993 13:12:25
     *
     * @param  string|integer|Zend_Date  $minute  Minute to add
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function addMinute($minute, $locale = null)
    {
        return $this->_minute('add', $minute, $locale);
    }


    /**
     * Subtracts minutes from the existing date object.
     * The minute is always a number.
     * Returned is the new date object
     * Example: 04.May.1993 13:07:25 -> subMinute(9); -> 04.May.1993 12:58:25
     *
     * @param  string|integer|Zend_Date  $minute  Minute to sub
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function subMinute($minute, $locale = null)
    {
        return $this->_minute('sub', $minute, $locale);
    }


    /**
     * Compares the minute with the existing date object. 
     * For example: 10:30:25 -> compareMinute(30) -> 0
     * Returns if equal, earlier or later
     *
     * @param  string|integer|Zend_Date  $minute  Hour to compare
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareMinute($minute, $locale = null)
    {
        return $this->_minute('cmp', $minute, $locale);
    }


    /**
     * Returns the second as new date object
     * Example: 02.Feb.1986 10:30:25 -> 01.Jan.1970 00:00:25
     *
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getSecond($locale = null)
    {
        return $this->copyPart(Zend_Date::SECOND, $locale);
    }


    /**
     * Returns the calculated second
     *
     * @param  string                    $calc    Type of calculation to make
     * @param  string|integer|Zend_Date  $second  Second to calculate, when null the actual second is calculated
     * @param  string|Zend_Locale        $locale  Locale for parsing input
     * @return Zend_Date|integer
     * @throws Zend_Date_Exception
     */
    private function _second($calc, $second, $locale)
    {
        if (is_null($second)) {
            throw new Zend_Date_Exception('parameter $second must be set, null is not allowed');
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }

        $this->_setGmt(false);
        if ($second instanceof Zend_Date) {
            $second = $second->get(Zend_Date::SECOND_SHORT, $locale);
        } else if (!is_numeric($second)) {
            throw new Zend_Date_Exception("invalid hour ($second) operand", $second);
        }

        $return = $this->_calcdetail($calc, $second, Zend_Date::SECOND_SHORT, $locale);
        $this->_resetGmt();
        if ($calc != 'cmp') {
            return $this;
        }
        return $return;
    }


    /**
     * Sets new seconds to the existing date object.
     * The second is always a number.
     * Returned is the new date object
     * Example: 04.May.1993 13:07:25 -> setSecond(100); -> 04.May.1993 13:08:40
     *
     * @param  string|integer|Zend_Date  $second  Second to set
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function setSecond($second, $locale = null)
    {
        return $this->_second('set', $second, $locale);
    }


    /**
     * Adds seconds to the existing date object.
     * The second is always a number.
     * Returned is the new date object
     * Example: 04.May.1993 13:07:25 -> addSecond(65); -> 04.May.1993 13:08:30
     *
     * @param  string|integer|Zend_Date  $second  Second to add
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function addSecond($second, $locale = null)
    {
        return $this->_second('add', $second, $locale);
    }


    /**
     * Subtracts seconds from the existing date object.
     * The second is always a number.
     * Returned is the new date object
     * Example: 04.May.1993 13:07:25 -> subSecond(10); -> 04.May.1993 13:07:15
     *
     * @param  string|integer|Zend_Date  $second  Second to sub
     * @param  boolean                   $gmt     OPTIONAL true = UTC time, false = actual time zone
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date  new date
     * @throws Zend_Date_Exception
     */
    public function subSecond($second, $locale = null)
    {
        return $this->_second('sub', $second, $locale);
    }


    /**
     * Compares the second with the existing date object. 
     * For example: 10:30:25 -> compareSecond(25) -> 0
     * Returns if equal, earlier or later
     *
     * @param  string|integer|Zend_Date  $second  Second to compare
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareSecond($second, $locale = null)
    {
        return $this->_second('cmp', $second, $locale);
    }


    /**
     * Returns the precision for fractional seconds
     *
     * @return integer
     */
    public function getFractionalPrecision()
    {
        return $this->_Precision;
    }


    /**
     * Sets a new precision for fractional seconds
     *
     * @param  integer  $precision  Precision for the fractional datepart 3 = milliseconds
     * @throws Zend_Date_Exception
     */
    public function setFractionalPrecision($precision)
    {
        if (!intval($precision) or ($precision < 0) or ($precision > 9)) {
            throw new Zend_Date_Exception("precision ($precision) must be a positive integer less than 10", $precision);
        }
        $this->_Precision = (int) $precision;
    }


    /**
     * Returns the milliseconds of the date object
     *
     * @return integer
     */
    public function getMilliSecond()
    {
        return $this->_Fractional;
    }


    /**
     * Sets new milliseconds for the date object
     * Example: setMilliSecond(550, 2) -> equals +5 Sec +50 MilliSec 
     *
     * @param  integer|Zend_Date  $milli      OPTIONAL Millisecond to set, when null the actual millisecond is set
     * @param  integer            $precision  OPTIONAL Fraction precision of the given milliseconds
     * @return integer|string
     */
    public function setMilliSecond($milli = null, $precision = null)
    {
        if ($milli === null) {
            list($milli, $time) = explode(" ", microtime());
            $milli = intval($milli);
            $precision = 6;
        } else if (!is_numeric($milli)) {
            throw new Zend_Date_Exception("invalid milli second ($milli) operand", $milli);
        }
        
        if ($precision === null) {
            $precision = $this->_Precision;
        } else if (!is_int($precision) || $precision < 1 || $precision > 9) {
            throw new Zend_Date_Exception("precision ($precision) must be a positive integer less than 10", $precision);
        }

        $this->_Fractional = 0;
        $this->addMilliSecond($milli, $precision);
        return $this->_Fractional;
    }


    /**
     * Adds milliseconds to the date object
     *
     * @param  integer|Zend_Date  $milli      OPTIONAL Millisecond to add, when null the actual millisecond is added
     * @param  integer            $precision  OPTIONAL Fractional precision for the given milliseconds
     * @return integer|string
     */
    public function addMilliSecond($milli = null, $precision = null)
    {
        if ($milli === null) {
            list($milli, $time) = explode(" ", microtime());
            $milli = intval($milli);
        } else if (!is_numeric($milli)) {
            throw new Zend_Date_Exception("invalid milli second ($milli) operand", $milli);
        }
        
        if ($precision === null) {
            $precision = $this->_Precision;
        } else if (!is_int($precision) || $precision < 1 || $precision > 9) {
            throw new Zend_Date_Exception("precision ($precision) must be a positive integer less than 10", $precision);
        }

        if ($precision != $this->_Precision) {
            if ($precision > $this->_Precision) {
                $diff = $precision - $this->_Precision;
                $milli = (int) ($milli / (10 * $diff));
            } else {
                $diff = $this->_Precision - $precision;
                $milli = (int) ($milli * (10 * $diff));
            }
        }

        $this->_Fractional += $milli;
        // add/sub milliseconds + add/sub seconds

        $max = pow(10, $this->_Precision);
        // milli includes seconds
        if ($this->_Fractional > $max) {
            while ($this->_Fractional > $max) {
                $this->addSecond(1);
                $this->_Fractional -= $max;
            }
        }

        if ($this->_Fractional < 0) {
            while ($this->_Fractional < 0) {
                $this->subSecond(1);
                $this->_Fractional += $max; 
            }
        }
        return $this->_Fractional;
    }


    /**
     * Subtracts a millisecond
     *
     * @param  integer|Zend_Date  $milli  OPTIONAL Millisecond to sub, when null the actual millisecond is subtracted
     * @param  integer            $precision  OPTIONAL Fractional precision for the given milliseconds
     * @return integer
     */
    public function subMilliSecond($milli = null, $precision = null)
    {
        return $this->addMilliSecond(0 - $milli);
    }


    /**
     * Compares only the millisecond part, returning the difference
     *
     * @param  integer|Zend_Date  $milli  OPTIONAL Millisecond to compare, when null the actual millisecond is compared
     * @param  integer            $precision  OPTIONAL Fractional precision for the given milliseconds
     * @return integer
     */
    public function compareMilliSecond($milli = null, $precision = null)
    {
        if ($milli === null) {
            list($milli, $time) = explode(" ", microtime());
            $milli = intval($milli);
        } else if (!is_numeric($milli)) {
            throw new Zend_Date_Exception("invalid milli second ($milli) operand", $milli);
        }
        
        if ($precision === null) {
            $precision = $this->_Precision;
        } else if (!is_int($precision) || $precision < 1 || $precision > 9) {
            throw new Zend_Date_Exception("precision ($precision) must be a positive integer less than 10", $precision);
        }

        if ($precision === 0) {
            throw new Zend_Date_Exception('precision is 0');
        }

        if ($precision != $this->_Precision) {
            if ($precision > $this->_Precision) {
                $diff = $precision - $this->_Precision;
                $milli = (int) ($milli / (10 * $diff));
            } else {
                $diff = $this->_Precision - $precision;
                $milli = (int) ($milli * (10 * $diff));
            }
        }

        $comp = $this->_Fractional - $milli;
        if ($comp < 0) {
            return -1;
        } else if ($comp > 0) {
            return 1;
        }
        return 0;
    }


    /**
     * Returns the week as new date object using monday as begining of the week
     * Example: 12.Jan.2007 -> 08.Jan.1970 00:00:00
     *
     * @param $locale  string|Zend_Locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     */
    public function getWeek($locale = null)
    {
        return $this->copyPart(Zend_Date::WEEK, $locale);
    }


    /**
     * Returns the calculated week
     *
     * @param  string                    $calc    Type of calculation to make
     * @param  string|integer|Zend_Date  $week    Week to calculate, when null the actual week is calculated
     * @param  string|Zend_Locale        $locale  Locale for parsing input
     * @return Zend_Date|integer
     * @throws Zend_Date_Exception
     */
    private function _week($calc, $week, $locale)
    {
        if (is_null($week)) {
            throw new Zend_Date_Exception('parameter $week must be set, null is not allowed');
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }

        $this->_setGmt(false);
        if ($week instanceof Zend_Date) {
            // extract week from object
            $week = $week->get(Zend_Date::WEEK, $locale);
        } else if (!is_numeric($week)) {
            throw new Zend_Date_Exception("invalid week ($week) operand", $week);
        }

        $return = $this->_calcdetail($calc, $week, Zend_Date::WEEK, $locale);
        $this->_resetGmt();
        if ($calc != 'cmp') {
            return $this;
        }
        return $return;
    }


    /**
     * Sets a new week. The week is always a number. The day of week is not changed.
     * Returned is the new date object
     * Example: 09.Jan.2007 13:07:25 -> setWeek(1); -> 02.Jan.2007 13:07:25
     *
     * @param  string|integer|Zend_Date  $week    Week to set
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     * @throws Zend_Date_Exception
     */
    public function setWeek($week, $locale = null)
    {
        return $this->_week('set', $week, $locale);
    }


    /**
     * Adds a week. The week is always a number. The day of week is not changed.
     * Returned is the new date object
     * Example: 09.Jan.2007 13:07:25 -> addWeek(1); -> 16.Jan.2007 13:07:25
     *
     * @param  string|integer|Zend_Date  $week    Week to add
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     * @throws Zend_Date_Exception
     */
    public function addWeek($week, $locale = null)
    {
        return $this->_week('add', $week, $locale);
    }


    /**
     * Subtracts a week. The week is always a number. The day of week is not changed.
     * Returned is the new date object
     * Example: 09.Jan.2007 13:07:25 -> subWeek(1); -> 02.Jan.2007 13:07:25
     *
     * @param  string|integer|Zend_Date  $week    Week to sub
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return Zend_Date
     * @throws Zend_Date_Exception
     */
    public function subWeek($week, $locale = null)
    {
        return $this->_week('sub', $week, $locale);
    }


    /**
     * Compares only the week part, returning the difference
     * Returned is the new date object
     * Returns if equal, earlier or later
     * Example: 09.Jan.2007 13:07:25 -> compareWeek(2); -> 0
     *
     * @param  string|integer|Zend_Date  $week    Week to compare
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return integer  0 = equal, 1 = later, -1 = earlier
     * @throws Zend_Date_Exception
     */
    public function compareWeek($week, $locale = null)
    {
        return $this->_week('cmp', $week, $locale);
    }


    /**
     * Sets a new standard locale for the date object.
     * This locale will be used for all functions
     * Returned is the really set locale.
     * Example: 'de_XX' will be set to 'de' because 'de_XX' does not exist
     * 'xx_YY' will be set to 'root' because 'xx' does not exist
     * 
     * @param  string|Zend_Locale     $locale  OPTIONAL Locale for parsing input
     * @return string
     */
    public function setLocale($locale = null)
    {
        if ($locale instanceof Zend_Locale) {
            $this->_Locale = $locale;
        } else if (!$locale = Zend_Locale::isLocale($locale, true)) {
            throw new Zend_Date_Exception("Given locale ($locale) does not exist", $locale);
        } else {
            $this->_Locale = new Zend_Locale($locale);
        }
        
        return $this->getLocale();
    }


    /**
     * Returns the actual set locale
     * 
     * @return string
     */
    public function getLocale()
    {
        return $this->_Locale->toString();
    }


    /**
     * Returns the actual GMT setting
     * 
     * @return  integer
     */
    private function getGmt()
    {
        return (int) $this->_GMT;
    }


    /**
     * Sets a new GMT value
     *   NULL|-1  = Default behavior
     *   FALSE    = Date is a timezone offset date
     *   TRUE     = Date is a GMT date
     * 
     * @param  boolean  $gmt  OPTIONAL gmt to set, if null automatic GMT detection is set
     * @return  boolean|integer  previously set $this->_GMT value
     */
    private function setGmt($gmt = null)
    {
        $result = $this->_GMT;
        if (($gmt === null) or ((int) $gmt == -1)) {
            $this->_GMT = -1;
        } else if ($gmt === false) {
            $this->_GMT = false;
        } else if ($gmt === true) {
            $this->_GMT = true;
        } else {
            throw new Zend_Date_Exception("gmt ($gmt) has to be boolean or null or -1", $gmt);
        }
        return $result;
    }


    /**
     * Overrules a autosetting of GMT to fixed $gmt
     * 
     * @param   boolean  $gmt   new gmt to set
     * @param   boolean  $none  OPTIONAL  sets if the $gmt is set or only returned
     * @return  boolean  new $gmt is $none = true
     */
    private function _setGmt($gmt, $none = null)
    {
        if ($none === null) {
            if ((int) $this->_GMT === -1) {
                $this->_GMTRule = true;
                $this->_GMT     = $gmt;
            }
        } else if ((int) $gmt == -1) {
            return (int) $none;
        } else {
            return (int) $this->_GMT;
        }
    }


    /**
     * Resets GMT from Overruled to Auto
     */
    private function _resetGmt()
    {
        if ($this->_GMTRule === true) {
            $this->_GMT = -1;
        }
    }


    /**
     * Returns the actual DST setting
     * 
     * @return  integer
     */
    public function getDst()
    {
        return $this->_DST;
    }


    /**
     * Sets a new DST value
     *   NULL  = Default behavior
     *   FALSE = Do not use summer/wintertime
     *   TRUE  = Use summer/wintertime
     */
    public function setDst($dst = null)
    {
        if ($dst === null) {
            $this->_DST = -1;
        } else if ($dst === false) {
            $this->_DST = 0;
        } else if ($dst === true) {
            $this->_DST = 1;
        }

        throw new Zend_Date_Exception("dst ($dst) has to be boolean or null", $dst);
    }
}
