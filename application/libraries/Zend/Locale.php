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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Locale.php 2883 2007-01-18 05:56:31Z gavin $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Include needed Locale classes
 */
require_once 'Zend.php';
require_once 'Zend/Locale/Data.php';
require_once 'Zend/Locale/Format.php';


/**
 * @category   Zend
 * @package    Zend_Locale
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Locale {

    // Class wide Locale Constants
    private static $_LocaleData = array(
        'root'  => '',
        'aa_DJ' => '',
        'aa_ER' => '',
        'aa_ET' => '',
        'aa'    => '',
        'af_ZA' => 'iso-8859-1,windows-1252',
        'af'    => 'iso-8859-1,windows-1252',
        'am_ET' => '',
        'am'    => '',
        'ar_AE' => 'iso-8859-6,windows-1256',
        'ar_BH' => 'iso-8859-6,windows-1256',
        'ar_DZ' => 'iso-8859-6,windows-1256',
        'ar_EG' => 'iso-8859-6,windows-1256',
        'ar_IQ' => 'iso-8859-6,windows-1256',
        'ar_JO' => 'iso-8859-6,windows-1256',
        'ar_KW' => 'iso-8859-6,windows-1256',
        'ar_LB' => 'iso-8859-6,windows-1256',
        'ar_LY' => 'iso-8859-6,windows-1256',
        'ar_MA' => 'iso-8859-6,windows-1256',
        'ar_OM' => 'iso-8859-6,windows-1256',
        'ar_QA' => 'iso-8859-6,windows-1256',
        'ar_SA' => 'iso-8859-6,windows-1256',
        'ar_SD' => 'iso-8859-6,windows-1256',
        'ar_SY' => 'iso-8859-6,windows-1256',
        'ar_TN' => 'iso-8859-6,windows-1256',
        'ar_YE' => 'iso-8859-6,windows-1256',
        'ar'    => 'iso-8859-6,windows-1256',
        'as_IN' => '',
        'as'    => '',
        'az_AZ' => '',
        'az'    => '',
        'be_BY' => 'iso-8859-5',
        'be'    => 'iso-8859-5',
        'bg_BG' => 'iso-8859-5',
        'bg'    => 'iso-8859-5',
        'bn_IN' => '',
        'bn'    => '',
        'bs_BA' => '',
        'bs'    => '',
        'byn_ER'=> '',
        'byn'   => '',
        'ca_ES' => 'iso-8859-1,windows-1252',
        'ca'    => 'iso-8859-1,windows-1252',
        'cs_CZ' => 'iso-8859-2',
        'cs'    => 'iso-8859-2',
        'cy_GB' => '',
        'cy'    => '',
        'da_DK' => 'iso-8859-1,windows-1252',
        'da'    => 'iso-8859-1,windows-1252',
        'de_AT' => 'iso-8859-1,windows-1252',
        'de_BE' => 'iso-8859-1,windows-1252',
        'de_CH' => 'iso-8859-1,windows-1252',
        'de_DE' => 'iso-8859-1,windows-1252',
        'de_LI' => 'iso-8859-1,windows-1252',
        'de_LU' => 'iso-8859-1,windows-1252',
        'de'    => 'iso-8859-1,windows-1252',
        'dv_MV' => '',
        'dv'    => '',
        'dz_BT' => '',
        'dz'    => '',
        'el_CY' => 'iso-8859-7',
        'el_GR' => 'iso-8859-7',
        'el'    => 'iso-8859-7',
        'en_AS' => 'iso-8859-1,windows-1252',
        'en_AU' => 'iso-8859-1,windows-1252',
        'en_BE' => 'iso-8859-1,windows-1252',
        'en_BW' => 'iso-8859-1,windows-1252',
        'en_BZ' => 'iso-8859-1,windows-1252',
        'en_CA' => 'iso-8859-1,windows-1252',
        'en_GB' => 'iso-8859-1,windows-1252',
        'en_GU' => 'iso-8859-1,windows-1252',
        'en_HK' => 'iso-8859-1,windows-1252',
        'en_IE' => 'iso-8859-1,windows-1252',
        'en_IN' => 'iso-8859-1,windows-1252',
        'en_JM' => 'iso-8859-1,windows-1252',
        'en_MH' => 'iso-8859-1,windows-1252',
        'en_MP' => 'iso-8859-1,windows-1252',
        'en_MT' => 'iso-8859-1,windows-1252',
        'en_NZ' => 'iso-8859-1,windows-1252',
        'en_PH' => 'iso-8859-1,windows-1252',
        'en_PK' => 'iso-8859-1,windows-1252',
        'en_SG' => 'iso-8859-1,windows-1252',
        'en_TT' => 'iso-8859-1,windows-1252',
        'en_UM' => 'iso-8859-1,windows-1252',
        'en_US' => 'iso-8859-1,windows-1252',
        'en_VI' => 'iso-8859-1,windows-1252',
        'en_ZA' => 'iso-8859-1,windows-1252',
        'en_ZW' => 'iso-8859-1,windows-1252',
        'en'    => 'iso-8859-1,windows-1252',
        'eo'    => 'iso-8859-3',
        'es_AR' => 'iso-8859-1,windows-1252',
        'es_BO' => 'iso-8859-1,windows-1252',
        'es_CL' => 'iso-8859-1,windows-1252',
        'es_CO' => 'iso-8859-1,windows-1252',
        'es_CR' => 'iso-8859-1,windows-1252',
        'es_DO' => 'iso-8859-1,windows-1252',
        'es_EC' => 'iso-8859-1,windows-1252',
        'es_ES' => 'iso-8859-1,windows-1252',
        'es_GT' => 'iso-8859-1,windows-1252',
        'es_HN' => 'iso-8859-1,windows-1252',
        'es_MX' => 'iso-8859-1,windows-1252',
        'es_NI' => 'iso-8859-1,windows-1252',
        'es_PA' => 'iso-8859-1,windows-1252',
        'es_PE' => 'iso-8859-1,windows-1252',
        'es_PR' => 'iso-8859-1,windows-1252',
        'es_PY' => 'iso-8859-1,windows-1252',
        'es_SV' => 'iso-8859-1,windows-1252',
        'es_US' => 'iso-8859-1,windows-1252',
        'es_UY' => 'iso-8859-1,windows-1252',
        'es_VE' => 'iso-8859-1,windows-1252',
        'es'    => 'iso-8859-1,windows-1252',
        'et_EE' => 'iso-8859-15',
        'et'    => 'iso-8859-15',
        'eu_ES' => 'iso-8859-1,windows-1252',
        'eu'    => 'iso-8859-1,windows-1252',
        'fa_AF' => '',
        'fa_IR' => '',
        'fa'    => '',
        'fi_FI' => 'iso-8859-1,windows-1252',
        'fi'    => 'iso-8859-1,windows-1252',
        'fo_FO' => 'iso-8859-1,windows-1252',
        'fo'    => 'iso-8859-1,windows-1252',
        'fr_BE' => 'iso-8859-1,windows-1252',
        'fr_CA' => 'iso-8859-1,windows-1252',
        'fr_CH' => 'iso-8859-1,windows-1252',
        'fr_FR' => 'iso-8859-1,windows-1252',
        'fr_LU' => 'iso-8859-1,windows-1252',
        'fr_MC' => 'iso-8859-1,windows-1252',
        'fr'    => 'iso-8859-1,windows-1252',
        'ga_IE' => 'iso-8859-1,windows-1252',
        'ga'    => 'iso-8859-1,windows-1252',
        'gez_ER'=> '',
        'gez_ET'=> '',
        'gez'   => '',
        'gl_ES' => 'iso-8859-1,windows-1252',
        'gl'    => 'iso-8859-1,windows-1252',
        'gu_IN' => '',
        'gu'    => '',
        'gv_GB' => '',
        'gv'    => '',
        'haw_US'=> '',
        'haw'   => '',
        'he_IL' => '',
        'he'    => '',
        'hi_IN' => '',
        'hi'    => '',
        'hr_HR' => 'iso-8859-2,windows-1250',
        'hr'    => 'iso-8859-2,windows-1250',
        'hu_HU' => 'iso-8859-2',
        'hu'    => 'iso-8859-2',
        'hy_AM' => '',
        'hy'    => '',
        'id_ID' => '',
        'id'    => '',
        'is_IS' => 'iso-8859-1,windows-1252',
        'is'    => 'iso-8859-1,windows-1252',
        'it_CH' => 'iso-8859-1,windows-1252',
        'it_IT' => 'iso-8859-1,windows-1252',
        'it'    => 'iso-8859-1,windows-1252',
        'iu'    => '',
        'ja_JP' => 'shift_jis,iso-2022-jp,euc-jp',
        'ja'    => 'shift_jis,iso-2022-jp,euc-jp',
        'ka_GE' => '',
        'ka'    => '',
        'kk_KZ' => '',
        'kk'    => '',
        'kl_GL' => '',
        'kl'    => '',
        'km_KH' => '',
        'km'    => '',
        'kn_IN' => '',
        'kn'    => '',
        'ko_KR' => 'euc-kr',
        'ko'    => 'euc-kr',
        'kok_IN'=> '',
        'kok'   => '',
        'kw_GB' => '',
        'kw'    => '',
        'ky_KG' => '',
        'ky'    => '',
        'lo_LA' => '',
        'lo'    => '',
        'lt_LT' => 'iso-8859-13,windows-1257',
        'lt'    => 'iso-8859-13,windows-1257',
        'lv_LV' => 'iso-8859-13,windows-1257',
        'lv'    => 'iso-8859-13,windows-1257',
        'mk_MK' => 'iso-8859-5,windows-1251',
        'mk'    => 'iso-8859-5,windows-1251',
        'ml_IN' => '',
        'ml'    => '',
        'mn_MN' => '',
        'mn'    => '',
        'mr_IN' => '',
        'mr'    => '',
        'ms_BN' => '',
        'ms_MY' => '',
        'ms'    => '',
        'mt_MT' => 'iso-8859-3',
        'mt'    => 'iso-8859-3',
        'nb_NO' => '',
        'nb'    => '',
        'nl_BE' => 'iso-8859-1,windows-1252',
        'nl_NL' => 'iso-8859-1,windows-1252',
        'nl'    => 'iso-8859-1,windows-1252', 
        'no_NO' => 'iso-8859-1,windows-1252',
        'no'    => 'iso-8859-1,windows-1252',
        'om_ET' => '',
        'om_KE' => '',
        'om'    => '',
        'or_IN' => '',
        'or'    => '',
        'pa_IN' => '',
        'pa'    => '',
        'pl_PL' => 'iso-8859-2',
        'pl'    => 'iso-8859-2',
        'ps_AF' => '',
        'ps'    => '',
        'pt_BR' => 'iso-8859-1,windows-1252',
        'pt_PT' => 'iso-8859-1,windows-1252',
        'pt'    => 'iso-8859-1,windows-1252',
        'ro_RO' => 'iso-8859-2',
        'ro'    => 'iso-8859-2',
        'ru_RU' => 'koi8-r,iso-8859-5',
        'ru_UA' => 'koi8-r,iso-8859-5',
        'ru'    => 'koi8-r,iso-8859-5',
        'sa_IN' => '',
        'sa'    => '',
        'sh_BA' => '',
        'sh_CS' => '',
        'sh_YU' => '',
        'sh'    => '',
        'sid_ET'=> '',
        'sid'   => '',
        'sk_SK' => 'iso-8859-2',
        'sk'    => 'iso-8859-2',
        'sl_SI' => 'iso-8859-2,windows-1250',
        'sl'    => 'iso-8859-2,windows-1250',
        'so_DJ' => '',
        'so_ET' => '',
        'so_KE' => '',
        'so_SO' => '',
        'so'    => '',
        'sq_AL' => 'iso-8859-1,windows-1252',
        'sq'    => 'iso-8859-1,windows-1252',
        'sr_BA' => 'windows-1251,iso-8859-5,iso-8859-2,windows-1250',
        'sr_CS' => 'windows-1251,iso-8859-5,iso-8859-2,windows-1250',
        'sr_YU' => 'windows-1251,iso-8859-5,iso-8859-2,windows-1250',
        'sr'    => 'windows-1251,iso-8859-5,iso-8859-2,windows-1250',
        'sv_FI' => 'iso-8859-1,windows-1252',
        'sv_SE' => 'iso-8859-1,windows-1252',
        'sv'    => 'iso-8859-1,windows-1252',
        'sw_KE' => '',
        'sw_TZ' => '',
        'sw'    => '',
        'syr_SY'=> '',
        'syr'   => '',
        'ta_IN' => '',
        'ta'    => '',
        'te_IN' => '',
        'te'    => '',
        'th_TH' => '',
        'th'    => '',
        'ti_ER' => '',
        'ti_ET' => '',
        'ti'    => '',
        'tig_ER'=> '',
        'tig'   => '',
        'tr_TR' => 'iso-8859-9,windows-1254',
        'tr'    => 'iso-8859-9,windows-1254',
        'tt_RU' => '',
        'tt'    => '',
        'uk_UA' => 'iso-8859-5',
        'uk'    => 'iso-8859-5',
        'ur_PK' => '',
        'ur'    => '',
        'uz_AF' => '',
        'uz_UZ' => '',
        'uz'    => '',
        'vi_VN' => '',
        'vi'    => '',
        'wal_ET'=> '',
        'wal'   => '',
        'zh_CN' => '',
        'zh_HK' => '',
        'zh_MO' => '',
        'zh_SG' => '',
        'zh_TW' => '',
        'zh'    => ''
    );


    /**
     * 
     */
    private static $_LocaleTranslation = array(
        'Australia'       => 'AU',
        'Austria'         => 'AT',
        'Belgium'         => 'BE',
        'Brazil'          => 'BR',
        'Canada'          => 'CA',
        'China'           => 'CN',
        'Czech Republic'  => 'CZ',
        'Denmark'         => 'DK',
        'Finland'         => 'FI',
        'France'          => 'FR',
        'Germany'         => 'DE',
        'Greece'          => 'GR',
        'Hong Kong SAR'   => 'HK',
        'Hungary'         => 'HU',
        'Iceland'         => 'IS',
        'Ireland'         => 'IE',
        'Italy'           => 'IT',
        'Japan'           => 'JP',
        'Korea'           => 'KP',
        'Mexiko'          => 'MX',
        'The Netherlands' => 'NL',
        'New Zealand'     => 'NZ',
        'Norway'          => 'NO',
        'Poland'          => 'PL',
        'Portugal'        => 'PT',
        'Russia'          => 'RU',
        'Singapore'       => 'SG',
        'Slovakia'        => 'SK',
        'Spain'           => 'ES',
        'Sweden'          => 'SE',
        'Taiwan'          => 'TW',
        'Turkey'          => 'TR',
        'United Kingdom'  => 'GB',
        'United States'   => 'US',
        
        'Chinese'         => 'zh',
        'Czech'           => 'cs',
        'Danish'          => 'da',
        'Dutch'           => 'nl',
        'English'         => 'en',
        'Finnish'         => 'fi',
        'French'          => 'fr',
        'German'          => 'de',
        'Greek'           => 'el',
        'Hungarian'       => 'hu',
        'Icelandic'       => 'is',
        'Italian'         => 'it',
        'Japanese'        => 'ja',
        'Korean'          => 'ko',
        'Norwegian'       => 'no',
        'Polish'          => 'pl',
        'Portuguese'      => 'pt',
        'Russian'         => 'ru',
        'Slovak'          => 'sk',
        'Spanish'         => 'es',
        'Swedish'         => 'sv',
        'Turkish'         => 'tr'
    );


    /**
     * Autosearch constants
     */
    const BROWSER     = 1;
    const ENVIRONMENT = 2;
    const FRAMEWORK   = 3;


    /**
     * Actual set locale 
     */
    private $_Locale;


    /**
     * Actual set Codeset 
     */
    private $_Codeset;


    /**
     * Generates a locale object
     * If no locale is given a automatic search is done
     * Then the most probable locale will be automatically set
     * Search order is
     *  1. Given Locale
     *  2. HTTP Client
     *  3. Server Environment
     *  4. Framework Standard
     *
     * @param  string  $locale  OPTIONAL locale for parsing input
     * @return object
     */
    public function __construct($locale = null)
    {
        if ($locale instanceof Zend_Locale) {
            $locale = $locale->toString();
        }
        $this->setLocale($locale);
    }


    /**
     * Serialization Interface
     * 
     * @return string
     */
    public function serialize()
    {
        return serialize($this);
    }


    /**
     * Returns a string representation of the object
     * 
     * @return string
     */
    public function toString()
    {
        return (string) $this->_Locale;
    }


    /**
     * Returns a string representation of the object
     * Alias for toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }


    /**
     * Search the locale automatically and return all used locales
     * ordered by quality
     * 
     * Standard Searchorder is
     * - getBrowser
     * - getEnvironment
     * @todo - getFramework
     * 
     * @param $searchorder  - OPTIONAL searchorder
     * @param $fastsearch   - OPTIONAL returnes the first found locale array when true
     *                        otherwise all found default locales will be returned 
     * @return  locale - returns an array of all the mosta locale string
     */
    public function getDefault($searchorder = null, $fastsearch = null)
    {
        $languages = array();
        if ($searchorder == self::ENVIRONMENT) {

            $languages = $this->getEnvironment();
            if (empty($languages) or !$fastsearch) {
                $languages = array_merge($languages, $this->getFramework());
            }
            if (empty($languages) or !$fastsearch) {
                $languages = array_merge($languages, $this->getBrowser());
            }

        } else if ($searchorder == self::FRAMEWORK) {

            $languages = $this->getFramework();
            if (empty($languages) or !$fastsearch) {
                $languages = array_merge($languages, $this->getEnvironment());
            }
            if (empty($languages) or !$fastsearch) {
                $languages = array_merge($languages, $this->getBrowser());
            }

        } else {

            $languages = $this->getBrowser();
            if (empty($languages) or !$fastsearch) {
                $languages = array_merge($languages, $this->getEnvironment());
            }
            if (empty($languages) or !$fastsearch) {
                $languages = array_merge($languages, $this->getFramework());
            }

        }
        return $languages;
    }


    /**
     * Expects the Systems standard locale
     * 
     * For Windows:
     * f.e.: LC_COLLATE=C;LC_CTYPE=German_Austria.1252;LC_MONETARY=C
     * would be recognised as de_AT
     * 
     * @return array
     */
    public function getEnvironment()
    {
        $language = setlocale(LC_ALL, 0);
        $languages = explode(';', $language);
        $languagearray = array();
        
        foreach ($languages as $locale)
        {

            $language = substr($locale, strpos($locale, '='));
            if ($language != '=C') {

               $language = substr($language, 1, strpos($language, '.') - 1);
               $splitted = explode('_', $language);
               if (!empty(Zend_Locale::$_LocaleData[$language])) {
                   $languagearray[$language] = 1;
                   if (strlen($language) > 4) {
                       $languagearray[substr($language, 0, 2)] = 1;
                   }
                   continue;
               }

               if (!empty(Zend_Locale::$_LocaleTranslation[$splitted[0]])) {
                   if (!empty(Zend_Locale::$_LocaleTranslation[$splitted[1]])) {
                       $languagearray[Zend_Locale::$_LocaleTranslation[$splitted[0]] . '_'
                     . Zend_Locale::$_LocaleTranslation[$splitted[1]]] = 1;
                   }
                   $languagearray[Zend_Locale::$_LocaleTranslation[$splitted[0]]] = 1;
               }
            }            
        }
        return $languagearray;
    }

    /**
     * Return an array of all accepted languages of the client
     * Expects RFC compilant Header !!
     * 
     * The notation can be :
     * de,en-UK-US;q=0.5,fr-FR;q=0.2
     * 
     * @return array - list of accepted languages including quality
     */
    public function getBrowser()
    {
        $httplanguages = getenv("HTTP_ACCEPT_LANGUAGE");

        $languages = array();
        if (empty($httplanguages)) {
            return $languages;
        }

        $accepted = preg_split('/,\s*/', $httplanguages);

        foreach ($accepted as $accept) {
            $result = preg_match('/^([a-z]{1,8}(?:[-_][a-z]{1,8})*)(?:;\s*q=(0(?:\.[0-9]{1,3})?|1(?:\.0{1,3})?))?$/i',
                                 $accept, $match);

            if (!$result) {
                continue;
            }

            if (isset($match[2])) {
                $quality = (float) $match[2];
            } else {
                $quality = 1.0;
            }

            $countrys = explode('-', $match[1]);
            $region = array_shift($countrys);

            $country2 = explode('_', $region);
            $region = array_shift($country2);

            foreach($countrys as $country) {
                $languages[$region . '_' . strtoupper($country)] = $quality;
            }
            foreach($country2 as $country) {
                $languages[$region . '_' . strtoupper($country)] = $quality;
            }
            $languages[$region] = $quality;
        }
        return $languages;
    }


    /**
     * Returns the locale which the framework is set to
     */
    public function getFramework()
    {
        $languages = array();
        return $languages;
    }


    /**
     * Sets a new locale
     * 
     * @param mixed  $locale  OPTIONAL new locale to set
     */
    public function setLocale($locale = null)
    {
        if (($locale == self::BROWSER) or ($locale == self::ENVIRONMENT) or ($locale === null)) {
            $locale = $this->getDefault($locale, true);
        }

        if (is_array($locale)) {
            $locale = key($locale);
        }
        
        if (!isset(Zend_Locale::$_LocaleData[$locale])) {
            $region = substr($locale, 0, 3);
            if (isset($region[2])) {
                if (($region[2] == '_') or ($region[2] == '-')) {
                    $region = substr($region, 0, 2);
                }
            }
            if (isset(Zend_Locale::$_LocaleData[$region])) {
                $this->_Locale = $region;
            } else {
                $this->_Locale = 'root';
            }
            
        } else {
            $this->_Locale = $locale;
        }
    }


    /**
     * Returns the language part of the locale
     * 
     * @return language
     */
    public function getLanguage()
    {
        $locale = explode('_', $this->_Locale);
        return $locale[0];
    }


    /**
     * Returns the region part of the locale if avaiable
     * 
     * @return region
     */
    public function getRegion()
    {
        $locale = explode('_', $this->_Locale);
        if (isset($locale[1])) {
            return $locale[1];
        }
        
        return false;
    }


    /**
     * Return the accepted charset of the client
     * @todo verify working
     */
    public function getHTTPCharset()
    {
        $httpcharsets = getenv("HTTP_ACCEPT_CHARSET");

        $charsets = array();
        if ($httpcharsets === false) {
            return $charsets;
        }

        $accepted = preg_split('/,\s*/', $httpcharsets);
        foreach ($accepted as $accept)
        {
            if (empty($accept)) {
                continue;
            }

            if (strpos($accept, ';'))
            {
                $quality = (float) substr($accept, strpos($accept, '=') + 1);
                $charsets[substr($accept, 0, strpos($accept, ';'))] = $quality;
            } else {
                $quality = 1.0;
                $charsets[$accept] = $quality;
            } 

        }

        return $charsets;
    }


    /**
     * Returns true if both locales are equal
     * 
     * @return boolean
     */
    public function equals($object)
    {
        if ($object->toString() == $this->toString()) {
            return true;
        }

        return false;
    }


    /**
     * Returns an array of languages translated for the actual locale
     * 
     * @param  string $locale - OPTIONAL locale for language translation
     * @return array
     */
    public function getLanguageList($locale = null)
    {
        if ($locale === null) {
            $locale = $this->_Locale;
        }

        return Zend_Locale_Data::getContent($locale, 'languagelist');
    }


    /**
     * Returns an single language translated for the actual locale
     * 
     * @param  string $language
     * @param  string $locale  OPTIONAL locale for language translation (defaults to $this locale)
     * @return array
     */
    public function getLanguageDisplay($language, $locale = null)
    {
        if ($locale === null) {
            $locale = $this->_Locale;
        }

        $language = Zend_Locale_Data::getContent($locale, 'language', $language);

        if (!empty($language)) {
            return current($language);
        }

        return false;
    }


    /**
     * Returns an array of scripts translated for the actual locale
     * 
     * @param  string $locale  OPTIONAL locale for language translation (defaults to $this locale)
     * @return array
     */
    public function getScriptList($locale = null)
    {
        if ($locale === null) {
            $locale = $this->_Locale;
        }

        return Zend_Locale_Data::getContent($locale, 'scriptlist');
    }


    /**
     * Returns a single script translated for a locale
     * 
     * @param  string $script
     * @param  string $locale  OPTIONAL locale for language translation (defaults to $this locale)
     * @return array
     */
    public function getScriptDisplay($script, $locale = null)
    {
        if ($locale === null) {
            $locale = $this->_Locale;
        }

        $script = Zend_Locale_Data::getContent($locale, 'script', $script);

        if (!empty($script)) {
            return current($script);
        }

        return false;
    }


    /**
     * Returns an array of regions translated for the actual locale
     * 
     * @param  string $locale  OPTIONAL locale for language translation (defaults to $this locale)
     * @return array
     */
    public function getRegionList($locale = null)
    {
        if ($locale === null) {
            $locale = $this->_Locale;
        }

        return Zend_Locale_Data::getContent($locale, 'territorylist');
    }


    /**
     * Returns an single region translated for the actual locale
     * 
     * @param  string  $region 
     * @param  string  $locale  OPTIONAL locale for language translation
     * @param  string  $locale  OPTIONAL locale for language translation (defaults to $this locale)
     * @return array
     */
    public function getRegionDisplay($region, $locale = null)
    {
        if ($locale === null) {
            $locale = $this->_Locale;
        }

        $region = Zend_Locale_Data::getContent($locale, 'territory', $region);

        if (!empty($region)) {
            return current($region);
        }

        return false;
    }


    /**
     * Returns an array of calendar names translated for the actual locale
     * 
     * @param  string $locale  OPTIONAL locale for language translation (defaults to $this locale)
     * @return array
     */
    public function getCalendarList($locale = null)
    {
        if ($locale === null) {
            $locale = $this->_Locale;
        }

        return Zend_Locale_Data::getContent($locale, 'type', 'calendar');
    }


    /**
     * Returns an single calendar name translated for the actual locale
     * 
     * @param  string  $calendar
     * @param  string  $locale  OPTIONAL locale for language translation (defaults to $this locale)
     * @return array
     */
    public function getCalendarDisplay($calendar, $locale = null)
    {
        if ($locale === null) {
            $locale = $this->_Locale;
        }

        $calendar = Zend_Locale_Data::getContent($locale, 'type', $calendar);

        if (!empty($calendar)) {
            return current($calendar);
        }

        return false;
    }


    /**
     * Returns an array with translated yes strings
     * 
     * @param  string  $locale  OPTIONAL locale for language translation (defaults to $this locale)
     * @return array
     */
    public function getQuestion($locale = null)
    {
        if ($locale === null) {
            $locale = $this->_Locale;
        }

        $quest = Zend_Locale_Data::getContent($locale, 'questionstrings');
        $yes = explode(':', $quest['yes']);
        $no  = explode(':', $quest['no']);
        $ret['yes']     = $yes[0];
        $ret['yesabbr'] = $yes[1];
        $ret['no']      = $no[0];
        $ret['noabbr']  = $no[1];
        return $ret;
    }


    /**
     * Checks if a locale identifier is a real locale or not
     * Examples:
     * "en_XX" refers to "en", which returns true
     * "XX_yy" refers to "root", which returns false
     * 
     * @param  string|Zend_Locale  $locale  Locale to check for
     * @param  boolean             $create  If true, create a default locale, if $locale is empty
     * @return false|string   false if given locale is not a locale, else the locale identifier is returned
     */
    public static function isLocale($locale, $create = false)
    {
        if (empty($locale) and ($create === true)) {
            $locale = new Zend_Locale();
        }
        if ($locale instanceof Zend_Locale) {
            return $locale->toString();
        }
        if (!is_string($locale)) {
            return false;
        }

        if (array_key_exists($locale, self::$_LocaleData)) {
            return $locale;
        } else {
            $locale = explode('_', $locale);
            if (array_key_exists($locale[0], self::$_LocaleData)) {
                return $locale;
            }
        }
        return false;
    }
}
