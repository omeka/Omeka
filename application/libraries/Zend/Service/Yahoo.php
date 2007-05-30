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
 * @package    Zend_Service
 * @subpackage Yahoo
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Zend_Service_Rest
 */
require_once 'Zend/Service/Rest.php';

/**
 * Zend_Service_YahooResult
 */
require_once 'Zend/Service/Yahoo/Result.php';

/**
 * Zend_Service_YahooResultSet
 */
require_once 'Zend/Service/Yahoo/ResultSet.php';

/**
 * Zend_Service_YahooImage
 */
require_once 'Zend/Service/Yahoo/Image.php';

/**
 * Zend_Service_YahooImageResult
 */
require_once 'Zend/Service/Yahoo/ImageResult.php';

/**
 * Zend_Service_YahooImageResultSet
 */
require_once 'Zend/Service/Yahoo/ImageResultSet.php';

/**
 * Zend_Service_YahooLocalResult
 */
require_once 'Zend/Service/Yahoo/LocalResult.php';

/**
 * Zend_Service_YahooLocalResultSet
 */
require_once 'Zend/Service/Yahoo/LocalResultSet.php';

/**
 * Zend_Service_YahooNewsResult
 */
require_once 'Zend/Service/Yahoo/NewsResult.php';

/**
 * Zend_Service_YahooNewsResultSet
 */
require_once 'Zend/Service/Yahoo/NewsResultSet.php';

/**
 * Zend_Service_YahooWebResult
 */
require_once 'Zend/Service/Yahoo/WebResult.php';

/**
 * Zend_Service_YahooWebResultSet
 */
require_once 'Zend/Service/Yahoo/WebResultSet.php';

/**
 * Zend_Filter
 */
require_once 'Zend/Filter.php';


/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Yahoo
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Yahoo {
    /**
     * Yahoo Developer Application ID
     *
     * @var string
     */
    public $appId;

    /**
     * Zend_Service_Rest instance
     *
     * @var Zend_Service_Rest
     */
    protected $_rest;

    /**
     * Constructs a new search object for the given application id.
     *
     * @param string $appid specified the developer's appid
     */
    public function __construct($appid)
    {
        $this->appId = $appid;
        $this->_rest = new Zend_Service_Rest();
        $this->_rest->setUri("http://api.search.yahoo.com");
    }


    /**
     * Perform a search of images.  The most basic query consists simply
     * of a plain text search, but you can also specify the type of
     * image, the format, color, etc.
     *
     * The specific options are:
     * 'type'       => (all|any|phrase)  How to parse the query terms
     * 'results'    => int  How many results to return, max is 50
     * 'start'      => int  The start offset for search results
     * 'format'     => (any|bmp|gif|jpeg|png)  The type of images to search for
     * 'coloration' => (any|color|bw)  The coloration of images to search for
     * 'adult_ok'   => bool  Flag to allow 'adult' images.
     *
     * @param string $query the query to be run
     * @param array $options an optional array of query options.
     * @return Zend_Service_Yahoo_ImageResultSet the search results
     */
    public function imageSearch($query, $options = NULL)
    {
        static $default_options = array('type'       => 'all',
                                        'results'    => 10,
                                        'start'      => 1,
                                        'format'     => 'any',
                                        'coloration' => 'any');

       	$options = $this->_prepareOptions($query, $options, $default_options);

        $this->_validateImageSearch($options);

        $this->_rest = new Zend_Service_Rest;

        $this->_rest->setUri('api.search.yahoo.com');

        $response = $this->_rest->restGet('/ImageSearchService/V1/imageSearch', $options);

        if ($response->isError()) {
        	throw new Zend_Service_Exception('An error occurred sending request. Status code: ' .
        	                                 $response->getStatus());
        }

        $dom = new DOMDocument();
        $dom->loadXML($response->getBody());

        self::_checkErrors($dom);

        return new Zend_Service_Yahoo_ImageResultSet($dom);
    }


    /**
     * Perform a search on local.yahoo.com.  The basic search
     * consists of a query and some fragment of location information;
     * for example zipcode, latitude/longitude, or street address.
     *
     * Query options include:
     * 'results'    => int  How many results to return, max is 50
     * 'start'      => int  The start offset for search results
     * 'sort'   => (relevance|title|distance|rating) How to order your results
     *
     * 'radius'     => float  The raidus (in miles) in which to search
     *
     * 'longitude'  => float  The longitude of the location to search around
     * 'latitude'   => float  The latitude of the location to search around
     *
     * 'zip'        => string The zipcode to search around
     *
     * 'street'     => string  The street address to search around
     * 'city'       => string  The city for address search
     * 'state'      => string  The state for address search
     * 'location'   => string  An adhoc location string to search around
     *
     * @param string $query  The query string you want to run
     * @param array  $options  The search options, including location
     * @return Zend_Service_Yahoo_LocalResultSet The results
     */
    public function localSearch($query, $options = NULL)
    {
        static $default_options = array('results' => 10,
                                        'start'   => 1,
                                        'sort'    => 'distance',
                                        'radius'  => 5);

        $options = $this->_prepareOptions($query, $options, $default_options);

        $this->_validateLocalSearch($options);

        $this->_uri->setHost('api.local.yahoo.com');

        $response = $this->_rest->restGet('/LocalSearchService/V1/localSearch', $options);

        if ($response->isError()) {
        	throw new Zend_Service_Exception('An error occurred sending request. Status code: ' .
        	                                 $response->getStatus());
        }

        $dom = new DOMDocument();
        $dom->loadXML($response->getBody());

        self::_checkErrors($dom);

        return new Zend_Service_Yahoo_LocalResultSet($dom);
    }


    /**
     * Execute a search on news.yahoo.com. This method minimally takes a
     * text query to search on.
     *
     * Query options coonsist of:
     *
     * 'results'    => int  How many results to return, max is 50
     * 'start'      => int  The start offset for search results
     * 'sort'       => (rank|date)  How to order your results
     * 'language'   => lang  The target document language to match
     * 'type'       => (all|any|phrase)  How the query should be parsed
     * 'site'       => string  A site to which your search should be restricted
     *
     * @param string $query  The query to run
     * @param array $options  The array of optional parameters
     * @return Zend_Service_Yahoo_NewsResultSet  The query return set
     */
    public function newsSearch($query, $options = NULL)
    {
        static $default_options = array('type' => 'all', 'start' => 1, 'sort' => 'rank', 'language' => 'en');

        $options = $this->_prepareOptions($query, $options, $default_options);

        $this->_validateNewsSearch($options);

        $this->_uri->setHost('api.search.yahoo.com');

        $response = $this->_rest->restGet('/NewsSearchService/V1/newsSearch', $options);

        if ($response->isError()) {
        	throw new Zend_Service_Exception('An error occurred sending request. Status code: ' .
        	                                 $response->getStatus());
        }

        $dom = new DOMDocument();
        $dom->loadXML($response->getBody());

        self::_checkErrors($dom);

        return new Zend_Service_Yahoo_NewsResultSet($dom);
    }


    /**
     * Perform a web content search on search.yahoo.com.  A basic query
     * consists simply of a text query.  Additional options that can be
     * specified consist of:
     * 'results'    => int  How many results to return, max is 50
     * 'start'      => int  The start offset for search results
     * 'language'   => lang  The target document language to match
     * 'type'       => (all|any|phrase)  How the query should be parsed
     * 'site'       => string  A site to which your search should be restricted
     * 'format'     => (any|html|msword|pdf|ppt|rss|txt|xls)
     * 'adult_ok'   => bool  permit 'adult' content in the search results
     * 'similar_ok' => bool  permit similar results in the result set
     * 'country'    => string  The country code for the content searched
     * 'license'    => (any|cc_any|cc_commercial|cc_modifiable)  The license of content being searched
     *
     * @param string $query  the query being run
     * @param array $options  any optional parameters
     * @return Zend_Service_Yahoo_WebResultSet  The return set
     */
    public function webSearch($query, $options = NULL)
    {
        static $default_options = array('type'     => 'all',
                                        'start'    => 1,
                                        'language' => 'en',
                                        'license'  => 'any',
                                        'results'  => 10,
                                        'format'   => 'any');

        $options = $this->_prepareOptions($query, $options, $default_options);
        $this->_validateWebSearch($options);

        $this->_uri->setHost('api.search.yahoo.com');

        $response = $this->_rest->restGet('/WebSearchService/V1/webSearch', $options);

        if ($response->isError()) {
        	throw new Zend_Service_Exception('An error occurred sending request. Status code: ' .
        	                                 $response->getStatus());
        }

        $dom = new DOMDocument();
        $dom->loadXML($response->getBody());

        self::_checkErrors($dom);

        return new Zend_Service_Yahoo_WebResultSet($dom);
    }

    /**
     * Validate Local Search Options
     *
     * @param array $options
     */
    protected function _validateLocalSearch($options)
    {
        $valid_options = array('appid', 'query', 'results', 'start', 'sort', 'radius', 'street',
                               'city', 'state', 'zip', 'location', 'latitude', 'longitude');
        if (!is_array($options)) {
            return;
        }

        $this->_compareOptions($options, $valid_options);

        if (isset($options['results'])) {
            if (!Zend_Filter::isBetween($options['results'], 1, 20, true)) {
                throw new Zend_Service_Exception($options['results'] . ' is not valid for the "results" option.');
            }
        }

        if (isset($options['start'])) {
            if (!Zend_Filter::isBetween($options['start'], 1, 1000, true)) {
                throw new Zend_Service_Exception($options['start'] . ' is not valid for the "start" option.');
            }
        }

        if (isset($options['longitude'])) {
            if (!Zend_Filter::isBetween($options['longitude'], -90, 90, true)) {
                throw new Zend_Service_Exception($options['longitude'] . ' is not valid for the "longitude" option.');
            }
        }
        if (isset($options['latitude'])) {
            if (!Zend_Filter::isBetween($options['latitude'], -180, 180, true)) {
                throw new Zend_Service_Exception($options['latitude'] . ' is not valid for the "latitude" option.');
            }
        }

        if (isset($options['zip'])) {
            if (!Zend_Filter::isZip($options['zip'])) {
                throw new Zend_Service_Exception($options['zip'] . ' is not a valid for the "zip" option.');
            }
        }

        $locationFields = array('street', 'city', 'state', 'zip', 'location', 'lon');
        foreach ($locationFields as $field)         {
            if (isset($options[$field]) && $options[$field] != '') {
                $locations[] = 1;
            }
        }

        if (!is_array($locations) && $options['latitude'] == '' && $options['longitude'] == '') {
            throw new Zend_Service_Exception('You must enter a locale to search near.');
        }

        if (!in_array($options['sort'], array('relevance', 'title', 'distance', 'rating'))) {
            throw new Zend_Service_Exception('You have entered an invalid sort property.');
        }
    }


    /**
     * Validate Image Search Options
     *
     * @param array $options
     */
    protected function _validateImageSearch($options)
    {
        $valid_options = array('appid', 'query', 'type', 'results', 'start', 'format', 'coloration', 'adult_ok');
        if (!is_array($options)) {
            return;
        }
        $this->_compareOptions($options, $valid_options);

        if (isset($options['type'])) {
            switch($options['type']) {
                case 'all':
                case 'any':
                case 'phrase':
                    break;
                default:
                    throw new Zend_Service_Exception("$type is an invalid type.");
            }
        }

        if (isset($options['results'])) {
            if (!Zend_Filter::isBetween($options['results'], 1, 50, true)) {
                throw new Zend_Service_Exception($options['results'] . ' is not valid for the "results" option.');
            }
        }

        if (isset($options['start'])) {
            if (!Zend_Filter::isBetween($options['start'], 1, 1000, true)) {
                throw new Zend_Service_Exception($options['start'] . ' is not valid for the "start" option.');
            }
        }

        if (isset($options['format'])) {
            switch($options['format']) {
                case 'any':
                case 'bmp':
                case 'gif':
                case 'jpeg':
                case 'png':
                    break;
                default:
                    throw new Zend_Service_Exception("$format is an invalid type");
            }
        }
        if (isset($options['coloration'])) {
            switch($options['coloration']) {
                case 'any':
                case 'color':
                case 'bw':
                    break;
                default:
                    throw new Zend_Service_Exception("$coloration is not a valid color");
            }
        }
    }


    /**
     * Validate News Search Options
     *
     * @param array $options
     */
    protected function _validateNewsSearch($options)
    {
        $valid_options = array('appid', 'query', 'results', 'start', 'sort', 'language', 'type', 'site');
        if (!is_array($options)) {
            return;
        }

        $this->_compareOptions($options, $valid_options);

        if (isset($options['results'])) {
            if (!Zend_Filter::isBetween($options['results'], 1, 20, true)) {
                throw new Zend_Service_Exception($options['results'] . ' is not valid for the "results" option.');
            }
        }
        if (isset($options['start'])) {
            if (!Zend_Filter::isBetween($options['start'], 1, 1000, true)) {
                throw new Zend_Service_Exception($options['start'] . ' is not valid for the "start" option.');
            }
        }

        if (isset($language)) {
            $this->_validateLanguage($language);
        }

        $this->_validateInArray('sort', $options['sort'], array('rank', 'date'));
        $this->_validateInArray('type', $options['type'], array('all', 'any', 'phrase'));

    }


    /**
     * Validate Web Search Options
     *
     * @param array $options
     */
    protected function _validateWebSearch($options)
    {
        $valid_options = array('appid', 'query', 'results', 'start', 'language', 'type', 'format', 'adult_ok',
                               'similar_ok', 'country', 'site', 'subscription', 'license');
        if (!is_array($options)) {
            return;
        }

        $this->_compareOptions($options, $valid_options);

        if (isset($options['results'])) {
            if (!Zend_Filter::isBetween($options['results'], 1, 20, true)) {
                throw new Zend_Service_Exception($options['results'] . ' is not valid for the "results" option.');
            }
        }
        if (isset($options['start'])) {
            if (!Zend_Filter::isBetween($options['start'], 1, 1000, true)) {
                throw new Zend_Service_Exception($options['start'] . ' is not valid for the "start" option.');
            }
        }

        $this->_validateLanguage($options['language']);

        $this->_validateInArray('query type', $options['type'], array('all', 'any', 'phrase'));
        $this->_validateInArray('format', $options['format'], array('any', 'html', 'msword', 'pdf', 'ppt', 'rss',
                                                                    'txt', 'xls'));
        $this->_validateInArray('license', $options['license'], array('any', 'cc_any', 'cc_commercial',
                                                                      'cc_modifiable'));
    }


    /**
     * Prepare options for sending to Yahoo!
     *
     * @param string $query Search Query
     * @param array $options User specified options
     * @param array $default_options Requied/Default options
     */
    protected function _prepareOptions($query, $options, $default_options)
    {
        $options['appid'] = $this->appId;
        $options['query'] = $query;
        $options = array_merge($default_options, $options);
        return $options;
    }


    /**
     * Utility function to check if input is an int and falls in a specified range.
     *
     * @param array $number 1d array, 'name' => int
     * @param string $type float or int
     * @param int $max maximum value
     * @param int $min minimum value (default 1)
     * @throws XN_Exception if number does not fall in range; with $name and valid range
     * @return boolean
    */
    protected function _validateNumber($name, $number, $max, $min=1)
    {
        if (isset($number) && (!is_numeric($number) || $number < $min || $number > $max)) {
            throw new Zend_Service_Exception("$name must be a" . ($min >= 0 ? ' positive ' : 'n ') . 'integer less than '
                                           . "$max and greater than $min");
        }
    }


   /**
    * Utility function to confirm chosen language is supported by Yahoo!
    *
    * @param string $lang Language code
    */
    protected function _validateLanguage($lang)
    {
        $languages = array ('ar', 'bg', 'ca', 'szh', 'tzh', 'hr', 'cs', 'da', 'nl', 'en',
                            'et', 'fi', 'fr', 'de', 'el', 'he', 'hu', 'is', 'id', 'it', 'ja', 'ko',
                            'lv', 'lt', 'no', 'fa', 'pl', 'pt', 'ro', 'ru', 'sk', 'sr', 'sl', 'es', 'sv', 'th', 'tr');
        if (!in_array($lang, $languages)) {
            throw new Zend_Service_Exception('The language selected is not a language offered.');
        }
    }


    /**
     * Utility function to check for a difference between two arrays.
     *
     * @param array $options User specified options
     * @param array $valid_options Valid options
     * @throws Zend_Service_Exception if difference is found (e.g. illegitimate query options)
     */
    protected function _compareOptions($options, $valid_options)
    {
        $difference = array_diff(array_keys($options), $valid_options);
        if ($difference) {
            throw new Zend_Service_Exception('The following parameters are invalid: ' . join(',', $difference));
        }
    }


    /**
     * Check that a named value is in the given array
     *
     * @param string $name Name associated with the value
     * @param mixed $value Value
     * @param array $array Array in which to check for the value
     * @throws Zend_Service_Exception
     */
    protected function _validateInArray($name, $value, $array)
    {
        if (!in_array($value, $array)) {
            throw new Zend_Service_Exception("You have entered an invalid value for $name");
        }
    }


    /**
     * Check if response is an error
     *
     * @param DOMDocument $dom DOM Object representing the result XML
     * @throws  Zend_Service_Exception Thrown when the result from Yahoo! is an error
     */
    static protected function _checkErrors(DOMDocument $dom)
    {
		$xpath = new DOMXPath($dom);
        $xpath->registerNamespace('yapi', 'urn:yahoo:api');

		if ($xpath->query("//yapi:Error")->length >= 1) {
			$message = $xpath->query('//yapi:Error/yapi:Message/text()')->item(0)->data;
			throw new Zend_Service_Exception($message);
		}
    }
}
