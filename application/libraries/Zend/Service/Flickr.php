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
 * @subpackage Flickr
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Zend_Service_Rest
 */
require_once 'Zend/Service/Rest.php';

/**
 * Zend_Service_Exception
 */
require_once 'Zend/Service/Exception.php';

/**
 * Zend_Service_Flickr_ResultSet
 */
require_once 'Zend/Service/Flickr/ResultSet.php';

/**
 * Zend_Service_Flickr_Result
 */
require_once 'Zend/Service/Flickr/Result.php';

/**
 * Zend_Service_Flickr_Image
 */
require_once 'Zend/Service/Flickr/Image.php';

/**
 * Zend_Filter
 */
require_once 'Zend/Filter.php';


/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Flickr
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Flickr
{
    /**
     * Your Flickr API key
     *
     * @var string
     */
    public $apiKey;

    /**
     * Zend_Service_Rest Object
     *
     * @var Zend_Service_Rest
     */
    protected $_rest;

    /**
     * Zend_Service_Flickr Constructor, setup character encoding
     *
     * @param string $apiKey Your Flickr API key
     */
    public function __construct($apiKey)
    {
        iconv_set_encoding('output_encoding', 'UTF-8');
        iconv_set_encoding('input_encoding', 'UTF-8');
        iconv_set_encoding('internal_encoding', 'UTF-8');

        $this->apiKey = $apiKey;
        $this->_rest = new Zend_Service_Rest();
        $this->_rest->setUri('http://www.flickr.com');
        $this->_array = array();
    }


    /**
     * Find Flickr photos by tag.
     *
     * Additional query options include:
     *    # per_page:  how many results to return per query
     *    # page:  the starting page offset.  first result will be (page -1)*per_page + 1
     *    # tag_mode: Either 'any' for an OR combination of tags, 
     *                or 'all' for an AND combination. Default is 'any'. 
     *    # min_upload_date: Minimum upload date to search on.  Date should be a unix timestamp.
     *    # max_upload_date: Maximum upload date to search on.  Date should be a unix timestamp.
     *    # min_taken_date: Minimum upload date to search on.  Date should be a MySQL datetime.
     *    # max_taken_date: Maximum upload date to search on.  Date should be a MySQL datetime.
     *
     * @param mixed $query A single tag or an array of tags.
     * @param array $options Additional parameters to refine your query.
     * @return Zend_Service_Flickr_ResultSet
     */
    public function tagSearch($query, $options = null)
    {
        static $method = 'flickr.photos.search';
        static $defaultOptions = array('per_page' => 10,
                                       'page'     => 1,
                                       'tag_mode' => 'or',
                                       'extras'   => 'license, date_upload, date_taken, owner_name, icon_server');


        // can't access by username, must get ID first
        $options['tags'] = is_array($query) ? implode(',', $query) : $query;

        $options = $this->_prepareOptions($method, $options, $defaultOptions);
        $this->_validateTagSearch($options);

        // now search for photos
        $response = $this->_rest->restGet('/services/rest/', $options);

        if ($response->isError()) {
        	throw new Zend_Service_Exception('An error occurered sending request. Status code: '
        	                               . $response->getStatus());
        }

		$dom = new DOMDocument();
        $dom->loadXML($response->getBody());

        self::_checkErrors($dom);

        return new Zend_Service_Flickr_ResultSet($dom, $this);
    }


    /**
     * Find Flickr photos by a user's username or email.
     *
     * Additional query options include:
     *    # per_page:  how many results to return per query
     *    # page:  the starting page offset.  first result will be (page - 1) * per_page + 1
     *    # min_upload_date: Minimum upload date to search on.  Date should be a unix timestamp.
     *    # max_upload_date: Maximum upload date to search on.  Date should be a unix timestamp.
     *    # min_taken_date: Minimum upload date to search on.  Date should be a MySQL datetime.
     *    # max_taken_date: Maximum upload date to search on.  Date should be a MySQL datetime.
     *
     * @param string $query username
     * @param array $options Additional parameters to refine your query.
     * @return Zend_Service_Flickr_ResultSet|boolean
     */
    public function userSearch($query, $options = null)
    {
        static $method = 'flickr.people.getPublicPhotos';
        static $defaultOptions = array('per_page' => 10,
                                       'page'     => 1,
                                       'extras'   => 'license, date_upload, date_taken, owner_name, icon_server');


        // can't access by username, must get ID first
        $user_id = false;
        if (strchr($query, '@')) {
            // optimistically hope this is an email
            $user_id = $this->getIdByEmail($query);
        }
        if (!$user_id) {
            // we can safely ignore this exception here
            $user_id = $this->getIdByUsername($query);
        }
        if (!$user_id) {
            return false;
        }
        $options['user_id'] = $user_id;


        $options = $this->_prepareOptions($method, $options, $defaultOptions);
        $this->_validateUserSearch($options);

        // now search for photos
        $response = $this->_rest->restGet('/services/rest/', $options);

        if ($response->isError()) {
        	throw new Zend_Service_Exception('An error occurered sending request. Status code: '
        	                               . $response->getStatus());
        }

		$dom = new DOMDocument();
        $dom->loadXML($response->getBody());

        self::_checkErrors($dom);

        return new Zend_Service_Flickr_ResultSet($dom, $this);
    }


    /**
     * Validate User Search Options
     *
     * @param array $options
     */
    protected function _validateUserSearch($options)
    {
        $valid_options = array('api_key', 'method', 'user_id', 'per_page', 'page', 'extras', 'min_upload_date',
                               'min_taken_date', 'max_upload_date', 'max_taken_date');
        if (!is_array($options)) {
            return;
        }

        $this->_compareOptions($options, $valid_options);


        if (!Zend_Filter::isBetween($options['per_page'], 1, 500, true)) {
            throw new Zend_Service_Exception($options['per_page'] . ' is not valid for the "per_page" option');
        }

        if (!Zend_Filter::isInt($options['page'])) {
            throw new Zend_Service_Exception($options['page'] . ' is not valid for the "page" option');
        }

        // validate extras, which are delivered in csv format
        if ($options['extras']) {
            $extras = explode(',', $options['extras']);
            $valid_extras = array('license', 'date_upload', 'date_taken', 'owner_name', 'icon_server');
            foreach($extras as $extra) {
                /**
                 * @todo The following does not do anything [yet], so it is commented out.
                 */
                //in_array(trim($extra), $valid_extras);
            }
        }
    }


    /**
     * Validate Tag Search Options
     *
     * @param array $options
     */
    protected function _validateTagSearch($options)
    {
        $valid_options = array('api_key', 'method', 'user_id', 'per_page', 'page', 'extras', 'min_upload_date',
                               'min_taken_date', 'max_upload_date', 'max_taken_date', 'tag_mode', 'tags');

        if (!is_array($options)) {
            return;
        }

        $this->_compareOptions($options, $valid_options);

        if (!Zend_Filter::isBetween($options['per_page'], 1, 500, true)) {
            throw new Zend_Service_Exception($options['per_page'] . ' is not valid for the "per_page" option');
        }

        if (!Zend_Filter::isInt($options['page'])) {
            throw new Zend_Service_Exception($options['page'] . ' is not valid for the "page" option');
        }

        // validate extras, which are delivered in csv format
        if ($options['extras']) {
            $extras = explode(',', $options['extras']);
            $valid_extras = array('license', 'date_upload', 'date_taken', 'owner_name', 'icon_server');
            foreach($extras as $extra) {
                /**
                 * @todo The following does not do anything [yet], so it is commented out.
                 */
                //in_array(trim($extra), $valid_extras);
            }
        }

    }


    /**
     * Utility function to find Flickr User IDs for usernames.
     *
     * (You can only find a user's photo with their NSID.)
     *
     * @param string $username the username
     * @return integer the NSID (userid)
     */
    public function getIdByUsername($username)
    {
        static $method = 'flickr.people.findByUsername';

        $options = array('api_key' => $this->apiKey, 'method' => $method, 'username' => $username);

        if (!empty($username)) {
        	$response = $this->_rest->restGet('/services/rest/', $options);

        	if ($response->isError()) {
        		throw new Zend_Service_Exception('An error occurered sending request. Status code: '
        		                               . $response->getStatus());
        	}

        	$dom = new DOMDocument();
        	$dom->loadXML($response->getBody());
        	self::_checkErrors($dom);
        	$xpath = new DOMXPath($dom);
        	return (string) $xpath->query('//user')->item(0)->getAttribute('id');
        } else {
            throw new Zend_Service_Exception('You must supply a username');
        }
    }


    /**
     * Utility function to find Flickr User IDs for emails.
     *
     * (You can only find a user's photo with their NSID.)
     *
     * @param string $email the email
     * @return integer the NSID (userid)
     */
    public function getIdByEmail($email)
    {
        static $method = 'flickr.people.findByEmail';

        $options = array('api_key' => $this->apiKey, 'method' => $method, 'find_email' => $email);

        if (!empty($email)) {
        	$response = $this->_rest->restGet('/services/rest/', $options);

        	if ($response->isError()) {
        		throw new Zend_Service_Exception('An error occurered sending request. Status code: '
        		                               . $response->getStatus());
        	}

        	$dom = new DOMDocument();
        	$dom->loadXML($response->getBody());
        	self::_checkErrors($dom);
        	$xpath = new DOMXPath($dom);
        	return (string) $xpath->query('//user')->item(0)->getAttribute('id');
        } else {
            throw new Zend_Service_Exception('You must supply an e-mail address');
        }
    }


    /**
     * Utility function to find Flickr photo details by ID.
     * @param string $id the NSID
     * @return Zend_Service_Flickr_Image the details for the specified image
     */
    public function getImageDetails($id)
    {
        static $method = 'flickr.photos.getSizes';

        $options = array('api_key' => $this->apiKey, 'method' => $method, 'photo_id' => $id);
        if (!empty($id)) {
            $response = $this->_rest->restGet('/services/rest/', $options);
            $dom = new DOMDocument();
            $dom->loadXML($response->getBody());
            $xpath = new DOMXPath($dom);
            self::_checkErrors($dom);
            $return = array();
            foreach($xpath->query('//size') as $size) {
                $label = (string) $size->getAttribute('label');
                $retval[$label] = new Zend_Service_Flickr_Image($size);
            }
        } else {
            throw new Zend_Service_Exception('You must supply a photo ID');
        }
        return $retval;
    }


    /**
     * Check Result for Errors
     *
     * @param DomDocument $dom
     * @throws Zend_Service_Exception
     */
    static protected function _checkErrors(DomDocument $dom)
    {
        if ($dom->documentElement->getAttribute('stat') == 'fail') {
            $xpath = new DomXpath($dom);
            $err = $xpath->query('//err')->item(0);
            throw new Zend_Service_Exception('Search failed due to error: ' . $err->getAttribute('msg')
                                           . ' (error #' . $err->getAttribute('code') . ')');
        }
    }


    /**
     * Prepare options for the request
     *
     * @param string $method Flickr Method to call
     * @param array $options User Options
     * @param array $defaultOptions Default Options
     * @return array Merged array of user and default/required options
     */
    protected function _prepareOptions($method, $options, $defaultOptions)
    {
        $options['method'] = $method;
        $options['api_key'] = $this->apiKey;
        $options = array_merge($defaultOptions, $options);
        return $options;
    }


    /**
     * Check whether the user options are valid
     *
     * @param array $options User options
     * @param array $validOptions Valid options
     */
    protected function _compareOptions($options, $validOptions)
    {
        $difference = array_diff(array_keys($options), $validOptions);
        if($difference) {
            throw new Zend_Service_Exception('The following parameters are invalid: ' . implode(',', $difference));
        }
    }
}

