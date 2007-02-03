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
 * @package    Zend_Locale
 * @subpackage Format
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Format.php 2883 2007-01-18 05:56:31Z gavin $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * include needed classes
 */
require_once 'Zend/Locale/Data.php';
require_once 'Zend/Locale/Exception.php';
require_once 'Zend/Locale/Math.php';


/**
 * @category   Zend
 * @package    Zend_Locale
 * @subpackage Format
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Locale_Format
{

    private static $_signs = array(
        'Arab' => array( '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'), // 0660 - 0669 arabic
        'Deva' => array( '०', '१', '२', '३', '४', '५', '६', '७', '८', '९'), // 0966 - 096F devanagari
        'Beng' => array( '০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'), // 09E6 - 09EF bengali
        'Guru' => array( '੦', '੧', '੨', '੩', '੪', '੫', '੬', '੭', '੮', '੯'), // 0A66 - 0A6F gurmukhi
        'Gujr' => array( '૦', '૧', '૨', '૩', '૪', '૫', '૬', '૭', '૮', '૯'), // 0AE6 - 0AEF gujarati
        'Orya' => array( '୦', '୧', '୨', '୩', '୪', '୫', '୬', '୭', '୮', '୯'), // 0B66 - 0B6F orija
        'Taml' => array( '௦', '௧', '௨', '௩', '௪', '௫', '௬', '௭', '௮', '௯'), // 0BE6 - 0BEF tamil
        'Telu' => array( '౦', '౧', '౨', '౩', '౪', '౫', '౬', '౭', '౮', '౯'), // 0C66 - 0C6F telugu
        'Knda' => array( '೦', '೧', '೨', '೩', '೪', '೫', '೬', '೭', '೮', '೯'), // 0CE6 - 0CEF kannada
        'Mlym' => array( '൦', '൧', '൨', '൩', '൪', '൫', '൬', '൭', '൮', '൯ '), // 0D66 - 0D6F malayalam
        'Tale' => array( '๐', '๑', '๒', '๓', '๔', '๕', '๖', '๗', '๘', '๙ '), // 0E50 - 0E59 thai
        'Laoo' => array( '໐', '໑', '໒', '໓', '໔', '໕', '໖', '໗', '໘', '໙'), // 0ED0 - 0ED9 lao
        'Tibt' => array( '༠', '༡', '༢', '༣', '༤', '༥', '༦', '༧', '༨', '༩ '), // 0F20 - 0F29 tibetan
        'Mymr' => array( '၀', '၁', '၂', '၃', '၄', '၅', '၆', '၇', '၈', '၉'), // 1040 - 1049 myanmar
        'Khmr' => array( '០', '១', '២', '៣', '៤', '៥', '៦', '៧', '៨', '៩'), // 17E0 - 17E9 khmer
        'Mong' => array( '᠐', '᠑', '᠒', '᠓', '᠔', '᠕', '᠖', '᠗', '᠘', '᠙'), // 1810 - 1819 mongolian
        'Limb' => array( '᥆', '᥇', '᥈', '᥉', '᥊', '᥋', '᥌', '᥍', '᥎', '᥏'), // 1946 - 194F limbu
        'Talu' => array( '᧐', '᧑', '᧒', '᧓', '᧔', '᧕', '᧖', '᧗', '᧘', '᧙'), // 19D0 - 19D9 tailue
        'Bali' => array( '᭐', '᭑', '᭒', '᭓', '᭔', '᭕', '᭖', '᭗', '᭘', '᭙'), // 1B50 - 1B59 balinese
        'Nkoo' => array( '߀', '߁', '߂', '߃', '߄', '߅', '߆', '߇', '߈', '߉')  // 07C0 - 07C9 nko
    );

    /**
     * Changes the numbers/digits within a given string from one script to another
     * When a script is not supported, no action is taken, so the original input will be returned
     * So this function works the same way like translating
     *
     * Examples for input:
     *   toNumberSystem('١١٠ Tests', 'Arab'); -> returns '100 Tests'
     * Example for not supported script
     *   toNumberSystem('١١٠ Tests', 'Unkn'); -> returns '١١٠ Tests'
     * 
     * @param  string  $input   String to convert
     * @param  string  $locale  Script to parse, see Zend_Locale->getScriptList() for details
     * @param  string  $to      OPTIONAL Script to convert to
     * @return string  Returns the converted input
     */
    public static function toNumberSystem($input, $from, $to = null)
    {
        if (isset(self::$_signs[$from])) {
            for ($X = 0; $X < 10; ++$X) {
                $source[$X + 10] = "/" . self::$_signs[$from][$X] . "/u";
            }
        } else {
            for ($X = 0; $X < 10; ++$X) {
                $source[$X + 10] = "/" . $X . "/";
            }
        }

        if (isset(self::$_signs[$to])) {
            for ($X = 0; $X < 10; ++$X) {
                $dest[$X + 10] = self::$_signs[$to][$X];
            }
        } else {
            for ($X = 0; $X < 10; ++$X) {
                $dest[$X + 10] = $X;
            }
        }

        return preg_replace($source, $dest, $input);
    }

    /**
     * Returns the first found number from an string
     * Parsing depends on given locale (grouping and decimal)
     *
     * Examples for input:
     * '  2345.4356,1234' = 23455456.1234
     * '+23,3452.123' = 233452.123
     * ' 12343 ' = 12343
     * '-9456km' = -9456
     * '0' = 0
     * '(-){0,1}(\d+(\.){0,1})*(\,){0,1})\d+'
     * 
     * @param  string              $input      Input string to parse for numbers
     * @param  integer             $precision  OPTIONAL precision of a float value, not touched if null
     * @param  string|Zend_Locale  $locale     OPTIONAL locale for parsing the number format
     * @return integer|string  Returns the extracted number
     */
    public static function getNumber($input, $precision = null, $locale = null)
    {
        if (!is_string($input))
            return $input;

        if (($locale == null) and (Zend_Locale::isLocale($precision))) {
            $locale    = $precision;
            $precision = null;
        }

        // Get correct signs for this locale
        $symbols = Zend_Locale_Data::getContent($locale,'numbersymbols');

        // Parse input locale aware
        $regex = '/(' . $symbols['minus'] . '){0,1}(\d+(\\' . $symbols['group'] . '){0,1})*(\\' .
                        $symbols['decimal'] . '){0,1}\d+/';
        preg_match($regex, $input, $found);
        if (!isset($found[0]))
            throw new Zend_Locale_Exception('No value in ' . $input . ' found');
        $found = $found[0];

        // Change locale input to be standard number
        if ($symbols['minus'] != "-")
            $found = strtr($found,$symbols['minus'],'-');
        $found = str_replace($symbols['group'],'', $found);

        // Do precision
        if (strpos($found, $symbols['decimal']) !== false) {
            if ($symbols['decimal'] != '.') {
                $found = str_replace($symbols['decimal'], ".", $found);
            }

            $pre = substr($found, strpos($found, '.') + 1);
            if ($precision === null) {
                $precision = strlen($pre);
            }

            if (strlen($pre) >= $precision) {
                $found = substr($found, 0, strlen($found) - strlen($pre) + $precision);
            }
        }

        return $found;
    }


    /**
     * Returns a locale formatted number
     * 
     * @param  string              $value      Number to localize
     * @param  integer             $precision  OPTIONAL Precision of a float value, not touched if null
     * @param  string|Zend_Locale  $locale     OPTIONAL Locale for parsing
     * @return string  locale formatted number
     */
    public static function toNumber($value, $precision = null, $locale = null)
    {
        if (($locale == null) and (Zend_Locale::isLocale($precision))) {
            $locale    = $precision;
            $precision = null;
        }

        // Get correct signs for this locale
        $symbols = Zend_Locale_Data::getContent($locale, 'numbersymbols');
        $format  = Zend_Locale_Data::getContent($locale, 'decimalnumberformat');
        $format  = $format['default'];
        iconv_set_encoding('internal_encoding', 'UTF-8');

        // seperate negative format pattern when avaiable 
        if (iconv_strpos($format, ';') !== false) {
            if (call_user_func(Zend_Locale_Math::$comp, $value, 0) < 0) {
                $format = iconv_substr($format, iconv_strpos($format, ';') + 1);
            } else {
                $format = iconv_substr($format, 0, iconv_strpos($format, ';'));
            }
        }

        // set negative sign
        if (call_user_func(Zend_Locale_Math::$comp, $value, 0) < 0) {
            if (iconv_strpos($format, '-') === false) {
                $format = $symbols['minus'] . $format;
            } else {
                $format = str_replace('-', $symbols['minus'], $format);
            }
        }

        // get number parts
        if (iconv_strpos($value, '.') !== false) {
            if ($precision === null) {
                $precision = iconv_substr($value, iconv_strpos($value, '.') + 1);
            } else {
                $precision = iconv_substr($value, iconv_strpos($value, '.') + 1, $precision);
            }
        } else {
            $precision = '';
        }

        // get fraction and format lengths
        call_user_func(Zend_Locale_Math::$scale, iconv_strlen($precision));
        $prec = call_user_func(Zend_Locale_Math::$sub, $value, call_user_func(Zend_Locale_Math::$sub, $value, '0', 0));
        if (iconv_strpos($prec, '-') !== false) {
            $prec = iconv_substr($prec, 1);
        }
        $number = call_user_func(Zend_Locale_Math::$sub, $value, 0, 0);
        if (iconv_strpos($number, '-') !== false) {
            $number = iconv_substr($number, 1);
        }
        $group  = iconv_strrpos($format, ',');
        $group2 = iconv_strpos ($format, ',');
        $point  = iconv_strpos ($format, '.');

        // Add fraction
        if ($prec == 0) {
            $format = iconv_substr($format, 0, $point) . iconv_substr($format, iconv_strrpos($format, '#') + 1);
        } else {
            $format = iconv_substr($format, 0, $point) . $symbols['decimal'] . iconv_substr($prec, 2).
                      iconv_substr($format, iconv_strrpos($format, '#') + 1);
        }

        // Add seperation
        if ($group == 0) {
            // no seperation
            $format = $number . iconv_substr($format, $point);
            
        } else if ($group == $group2) {
            
            // only 1 seperation
            $seperation = ($point - $group - 1);
            for ($x = iconv_strlen($number); $x > $group2; $x -= $seperation) {
                if (iconv_substr($number, 0, $x - $seperation) !== "") {
                     $number = iconv_substr($number, 0, $x - $seperation) . $symbols['group']
                             . iconv_substr($number, $x - $seperation);
                }
            }
            $format = iconv_substr($format, 0, iconv_strpos($format, '#')) . $number . iconv_substr($format, $point);
            
        } else {
            
            // 2 seperations
            if (iconv_strlen($number) > ($point - $group - 1)) { 
                $seperation = ($point - $group - 1);
                $number = iconv_substr($number, 0, iconv_strlen($number) - $seperation) . $symbols['group']
                        . iconv_substr($number, iconv_strlen($number) - $seperation);

                if ((iconv_strlen($number) - 1) > ($point - $group)) {
                    $seperation2 = ($group - $group2 - 1);
                    
                    for ($x = iconv_strlen($number) - $seperation2 - 2; $x > $seperation2; $x -= $seperation2) {
                         $number = iconv_substr($number, 0, $x - $seperation2) . $symbols['group']
                                 . iconv_substr($number, $x - $seperation2);
                    }
                }

            }
            $format = iconv_substr($format, 0, iconv_strpos($format, '#')) . $number . iconv_substr($format, $point);
            
        }
        return (string) $format;        
    }


    /**
     * Checks if the input contains a normalized or localized number
     * 
     * @param  string              $input      Localized number string
     * @param  string|Zend_Locale  $locale     OPTIONAL Locale for parsing
     * @return boolean  Returns true if a number was found
     */
    public static function isNumber($input, $locale = null)
    {
        // Get correct signs for this locale
        $symbols = Zend_Locale_Data::getContent($locale,'numbersymbols');

        // Parse input locale aware
        $regex = '/('.$symbols['minus'].'){0,1}(\d+(\\'.$symbols['group'].'){0,1})*(\\'.$symbols['decimal'].'){0,1}\d+/';
        preg_match($regex, $input, $found);

        if (!isset($found[0]))
            return false;
        return true;
    }


    /**
     * Alias for getNumber
     * 
     * @param  string              $value      Number to localize
     * @param  integer             $precision  OPTIONAL Precision of the float value, not touched if null
     * @param  string|Zend_Locale  $locale     OPTIONAL Locale for parsing
     * @return  float
     */
    public static function getFloat($input, $precision = null, $locale = null)
    {
        return floatval(self::getNumber($input, $precision, $locale));
    }


    /**
     * Returns a locale formatted integer number
     * Alias for toNumber()
     * 
     * @param  string              $value      Number to normalize
     * @param  integer             $precision  OPTIONAL Precision of a float value, not touched if null
     * @param  string|Zend_Locale  $locale     OPTIONAL Locale for parsing
     * @return string  Locale formatted number
     */
    public static function toFloat($value, $precision = null, $locale = null)
    {
        return self::toNumber($value, $precision, $locale);
    }


    /**
     * Returns if a float was found
     * Alias for isNumber()
     * 
     * @param  string              $input      Localized number string
     * @param  string|Zend_Locale  $locale     OPTIONAL Locale for parsing
     * @return boolean  Returns true if a number was found
     */
    public static function isFloat($value, $locale = null)
    {
        return self::isNumber($value, $locale);
    }


    /**
     * Returns the first found integer from an string
     * Parsing depends on given locale (grouping and decimal)
     *
     * Examples for input:
     * '  2345.4356,1234' = 23455456
     * '+23,3452.123' = 233452
     * ' 12343 ' = 12343
     * '-9456km' = -9456
     * '0' = 0
     * '(-){0,1}(\d+(\.){0,1})*(\,){0,1})\d+'
     * 
     * @param  string              $input      Input string to parse for numbers
     * @param  string|Zend_Locale  $locale     OPTIONAL locale for parsing the number format
     * @return integer  Returns the extracted number
     */
    public static function getInteger($input, $locale = null)
    {
        return intval(self::getFloat($input, 0, $locale));
    }


    /**
     * Returns a localized number
     * 
     * @param  string              $value      Number to normalize
     * @param  string|Zend_Locale  $locale     OPTIONAL Locale for parsing
     * @return string  Locale formatted number
     */
    public static function toInteger($value, $locale = null)
    {
        return self::toNumber($value, 0, $locale);
    }


    /**
     * Returns if a integer was found
     * 
     * @param  string              $input      Localized number string
     * @param  string|Zend_Locale  $locale     OPTIONAL Locale for parsing
     * @return boolean  Returns true if a integer was found
     */
    public static function isInteger($value, $locale = null)
    {
        return self::isNumber($value, $locale);
    }


    /**
     * Split numbers in proper array fields
     *
     * @param string  $number   Number to parse
     * @param string  $format   Format to parse. Only single-letter codes (H, m, s, y, M, d),
     *                          and MMMM and EEEE are supported.
     * @param Zend_Locale|string $locale  OPTIONAL Locale of $number, possibly in string form (e.g. 'de_AT')
     * @return array            possible array members: day, month, year, hour, minute, second
     */
    private static function _parseDate($number, $format, $locale)
    {
        $day   = iconv_strpos($format, 'd');
        $month = iconv_strpos($format, 'M');
        $year  = iconv_strpos($format, 'y');
        $hour  = iconv_strpos($format, 'H');
        $min   = iconv_strpos($format, 'm');
        $sec   = iconv_strpos($format, 's');
        if ($hour === false) {
            $hour = iconv_strpos($format, 'h');
        }

        if ($day !== false) {
            $parse[$day]   = 'd';
            $parse[$month] = 'M';
            $parse[$year]  = 'y';
        }
        if ($hour !== false) {
            $parse[$hour] = 'H';
            $parse[$min]  = 'm';
            if ($sec !== false) {
                $parse[$sec]  = 's';
            }
        }

        if (empty($parse)) {
            throw new Zend_Locale_Exception('unknown format, neither date nor time in ' . $format . ' found');
        }
        ksort($parse);

        // erase day string
        if (iconv_strpos($format, 'EEEE') !== false) {
            $daylist = Zend_Locale_Data::getContent($locale, 'daylist', array('gregorian', 'wide'));
            foreach($daylist as $key => $name) {
                if (iconv_strpos($number, $name) !== false) {
                    $number   = str_replace($name, "EEEE", $number);
                    break;
                }
            }
        }

        $monthlist = false;
        if (!empty($locale) && $month) {
            // prepare to convert month name to their numeric equivalents, if requested, and we have a $locale
            if (iconv_strpos($format, 'MMMM') !== false) {
                $monthlist = Zend_Locale_Data::getContent($locale, 'monthlist', array('gregorian', 'wide'));
            } else {
                $monthlist = Zend_Locale_Data::getContent($locale, 'monthlist', array('gregorian', 'abbreviated'));
            }
        }

        $position = false;

        // If $locale was invalid, $monthlist will default to a "root" identity
        // mapping for each month number from 1 to 12.
        // If no $locale was given, or $locale was invalid, do not use this identity mapping to normalize.
        // Otherwise, translate locale aware month names in $number to their numeric equivalents.
        if ($monthlist && $monthlist[1] != 1) {
            foreach($monthlist as $key => $name) {
                if (($position = iconv_strpos($number, $name)) !== false) {
                    if ($key < 10) {
                        $key = "0" . $key;
                    }
                    $number   = str_replace($name, $key, $number);
                    break;
                }
            }
        }

        // split number parts 
        $split = false;
        // @todo - document behavior of /u modifier within preg_match_all
        preg_match_all('/\d+/u', $number, $splitted);

        if (count($splitted[0]) == 0) {
            throw new Zend_Locale_Exception('No date part in ' . $number . ' found');
        }

        if (count($splitted[0]) == 1) {
            $split = 0;
        }
        $cnt = 0;

        foreach($parse as $key => $value) {

            switch($value) {
                case 'd':
                    if ($split === false) {
                        if (count($splitted[0]) > $cnt) {
                            $result['day']    = (int) $splitted[0][$cnt];
                        }
                    } else {
                        $result['day']    = (int) iconv_substr($splitted[0][0], $split, 2);
                        $split += 2;
                    }
                    ++$cnt;
                    break;
                case 'M':
                    if ($split === false) {
                        if (count($splitted[0]) > $cnt) {
                            $result['month']  = (int) $splitted[0][$cnt];
                        }
                    } else {
                        $result['month']  = (int) iconv_substr($splitted[0][0], $split, 2);
                        $split += 2;
                    }
                    ++$cnt;
                    break;
                case 'y':
                    $length = 2;
                    if (iconv_substr($format, $year, 4) == 'yyyy') {
                        $length = 4;
                    }
                    if ($split === false) {
                        if (count($splitted[0]) > $cnt) {
                            $result['year']   = (int) $splitted[0][$cnt];
                        }
                    } else {
                        $result['year']   = (int) iconv_substr($splitted[0][0], $split, $length);
                        $split += $length;
                    }
                    ++$cnt;
                    break;
                case 'H':
                    if ($split === false) {
                        if (count($splitted[0]) > $cnt) {
                            $result['hour']   = (int) $splitted[0][$cnt];
                        }
                    } else {
                        $result['hour']   = (int) iconv_substr($splitted[0][0], $split, 2);
                        $split += 2;
                    }
                    ++$cnt;
                    break;
                case 'm':
                    if ($split === false) {
                        if (count($splitted[0]) > $cnt) {
                            $result['minute'] = (int) $splitted[0][$cnt];
                        }
                    } else {
                        $result['minute'] = (int) iconv_substr($splitted[0][0], $split, 2);
                        $split += 2;
                    }
                    ++$cnt;
                    break;
                case 's':
                    if ($split === false) {
                        if (count($splitted[0]) > $cnt) {
                            $result['second'] = (int) $splitted[0][$cnt];
                        }
                    } else {
                        $result['second'] = (int) iconv_substr($splitted[0][0], $split, 2);
                        $split += 2;
                    }
                    ++$cnt;
                    break;
            }
        }

        if ($day !== false) {
            // fix false month
            if (isset($result['day']) and isset($result['month'])) {
                if (($position !== false) && ($position != $month)) {
                    $temp = $result['day'];
                    $result['day']   = $result['month'];
                    $result['month'] = $temp;
                }
            }

            // fix switched values d <> y
            if (isset($result['day']) and isset($result['year'])) {
                if ($result['day'] > 31) {
                    $temp = $result['year'];
                    $result['year'] = $result['day'];
                    $result['day']  = $temp;
                }
            }

            // fix switched values M <> y
            if (isset($result['month']) and isset($result['year'])) {
                if ($result['month'] > 31) {
                    $temp = $result['year'];
                    $result['year']  = $result['month'];
                    $result['month'] = $temp;
                }
            }

            // fix switched values M <> y
            if (isset($result['month']) and isset($result['day'])) {
                if ($result['month'] > 12) {
                    $temp = $result['day'];
                    $result['day']   = $result['month'];
                    $result['month'] = $temp;
                }
            }
        }
        return $result;
    }


    /**
     * Returns an array with the normalized date from an locale date
     * a input of 10.01.2006 without a $locale would return:
     * array ('day' => 10, 'month' => 1, 'year' => 2006)
     * The optional $locale parameter is only used to convert human readable day
     * and month names to their numeric equivalents.
     *
     * @param  string              $date    Date string
     * @param  string              $format  Date type CLDR format to parse. 
     *                                      Only single-letter codes (H, m, s, y, M, d), and MMMM and EEEE are supported.
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale of $number, possibly in string form (e.g. 'de_AT')
     * @return array                        Possible array members: day, month, year, hour, minute, second
     */
    public static function getDate($date, $format = null, $locale = null)
    {
        if (empty($format)) {
            $format = Zend_Locale_Data::getContent($locale, 'defdateformat', 'gregorian');
            $format = $format['default'];

            $format = Zend_Locale_Data::getContent($locale, 'dateformat', array('gregorian', $format));
            $format = $format['pattern'];
        }

        $date = self::_parseDate($date, $format, $locale);
        return $date;
    }

    /**
     * Returns if the given string is a date
     *
     * @param  string              $date    Date string
     * @param  string              $format  Date type CLDR format to parse. 
     *                                      Only single-letter codes (H, m, s, y, M, d), and MMMM and EEEE are supported.
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing the date string
     * @return boolean
     */
    public static function isDate($date, $format = null, $locale = null)
    {
        try {
            $date = self::getDate($date, $format, $locale);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }


    /**
     * Returns an array with 'hour', 'minute', and 'second' elements extracted from $time
     * according to the order described in $format.  For a format of 'H:m:s', and
     * an input of 11:20:55, getTime() would return:
     * array ('hour' => 11, 'minute' => 20, 'second' => 55)
     * The optional $locale parameter may be used to help extract times from strings
     * containing both a time and a day or month name.
     *
     * @param  string              $time    Time string
     * @param  string              $format  Date type CLDR format to parse. Only single-letter
     *                                      codes(H, m, s, y, M, d), and MMMM and EEEE are supported.
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale of $number, possibly in string form (e.g. 'de_AT')
     * @return array                        Possible array members: day, month, year, hour, minute, second
     */
    public static function getTime($time, $format = null, $locale = null)
    {
        if (empty($format)) {
            $format = Zend_Locale_Data::getContent($locale, 'deftimeformat', 'gregorian');
            $format = $format['default'];

            $format = Zend_Locale_Data::getContent($locale, 'timeformat', array('gregorian', $format));
            $format = $format['pattern'];
        }

        $time = self::_parseDate($time, $format, $locale);

        return $time;
    }


    /**
     * Returns is the given string is a time
     *
     * @param string $time    Time string
     * @param string $format  Time type CLDR format !!!
     * @param locale $locale  OPTIONAL Locale of time string
     * @return boolean
     */
    public static function isTime($time, $format = null, $locale = null)
    {
        try {
            $date = self::getTime($time, $format, $locale);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
}
