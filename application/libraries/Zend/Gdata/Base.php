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
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Zend_Gdata
 */
require_once 'Zend/Gdata.php';

/**
 * Zend_Gdata_InvalidArgumentException
 */
require_once 'Zend/Gdata/InvalidArgumentException.php';

/**
 * Gdata Base
 *
 * @link http://code.google.com/apis/base/
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Base extends Zend_Gdata
{
    const BASE_FEED_URI = 'http://www.google.com/base/feeds/snippets';
    const BASE_METADATA_TYPES_URI = 'http://www.google.com/base/feeds/itemtypes';
    const BASE_METADATA_ATTRIBUTES_URI = 'http://www.google.com/base/feeds/attributes';
    const BASE_POST_URI = 'http://www.google.com/base/feeds/items';

    protected $_developerKey = null;

    protected $_attributeQueryTerms = array();

    protected $_defaultPostUri = self::BASE_POST_URI;

    /**
     * Create Gdata_Calendar object
     *
     * @param string $email
     * @param string $password
     *
     */
    public function __construct($client = null, $key = null)
    {
        parent::__construct($client);
        if ($key != null) {
            $this->setDeveloperKey($key);
        }
    }

    /**
     * Sets developer key
     *
     * @param string $key
     */
    public function setDeveloperKey($key)
    {
        $this->_developerKey = substr($key, 0, strcspn($key, "\n\r"));
        $headers['X-Google-Key'] = 'key=' . $this->_developerKey;
        $this->_httpClient->setHeaders($headers);
    }

    /**
     * @return string developerKey
     */
    public function getDeveloperKey()
    {
        return $this->_developerKey;
    }

    /**
     * Retreive feed object
     *
     * @return Zend_Feed
     */
    public function getBaseFeed($uri = null)
    {
        if ($uri == null) {
            $uri = self::BASE_FEED_URI;
        }
        /*
        if (isset($this->_params['_entry'])) {
            $uri .= '/' . $this->_params['_entry'];
        } else
        */
        if (isset($this->_params['_category'])) {
            $uri .= '/-/' . $this->_params['_category'];
        }
        $uri .= $this->getQueryString();
        return parent::getFeed($uri);
    }

    /**
     * @param string locale
     * @return array item types
     */
    public function getItemTypeFeed($locale, $itemType = null)
    {
        $uri = self::BASE_METADATA_TYPES_URI;
        $uri .= '/' . $locale;
        if (isset($itemType)) {
            $uri .= '/' . $itemType;
        }
        return parent::getFeed($uri);
    }

    /**
     * @param string $value
     */
    public function getItemTypeAttributesFeed($itemType)
    {
        $uri = self::BASE_METADATA_ATTRIBUTES_URI;
        $this->query = $itemType;
        return parent::getFeed($uri);
    }

    /**
     * @return string querystring
     */
    protected function getQueryString()
    {
        $queryArray = array();
        if (isset($this->query)) {
            $bq = $this->query;
            $queryArray[] = $bq;
        }
        foreach (array_keys($this->_attributeQueryTerms) as $name) {
            foreach ($this->_attributeQueryTerms[$name] as $attr) {
                $op = ':';
                if (isset($attr['op'])) {
                    $op = $attr['op'];
                }
                $value = $attr['value'];
                $queryArray[] = "[$name $op $value]";
            }
        }
        $this->query = implode(' ', $queryArray);
        $queryString = parent::getQueryString();
        if (isset($bq)) {
            $this->query = $bq;
        }
        return $queryString;
    }

    /**
     * @param string $attributeName
     * @param string $attributeValue
     * @param string $op
     * @throws Zend_Gdata_InvalidArgumentException
     */
    public function addAttributeQuery($attributeName, $attributeValue, $op = ':')
    {
        if (!in_array($op, array(':', '==', '<', '>', '<=', '>=', '<<'))) {
            throw new Zend_Gdata_InvalidArgumentException("Unsupported attribute query comparison operator '$op'.");
        }
        $this->_attributeQueryTerms[$attributeName][] = array(
            'op' => $op,
            'value' => $attributeValue
        );
    }

    /**
     * @param string $attributeName
     */
    public function unsetAttributeQuery($attributeName = null)
    {
        if ($attributeName == null) {
            $this->_attributeQueryTerms = array();
        } else {
            unset($this->_attributeQueryTerms[$attributeName]);
        }
    }

    /**
     * @param string $value
     */
    public function setCategory($value)
    {
        $this->category = $value;
    }

    /**
     * @param string $value
     */
    public function setOrderby($value)
    {
        $this->orderby = $value;
    }

    /**
     * @return string category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return string orderby
     */
    public function getOrderby()
    {
        return $this->orderby;
    }

    /**
     * @param string $var
     * @param mixed $value
     */
    protected function __set($var, $value)
    {
        switch ($var) {
            case 'query':
                $var = 'bq';
                break;
            case 'category':
                $var = '_category';
                break;
            default:
                // other params are handled by the parent
                break;
        }

        parent::__set($var, $value);
    }

    /**
     * @param string $var
     * @return mixed value
     */
    protected function __get($var)
    {
        switch ($var) {
            case 'query':
                $var = 'bq';
                break;
            case 'category':
                $var = '_category';
                break;
            default:
                // other params are handled by the parent
                break;
        }
        return parent::__get($var);
    }

    /**
     * @param string $var
     * @return bool
     */
    protected function __isset($var)
    {
        switch ($var) {
            case 'query':
                $var = 'bq';
                break;
            case 'category':
                $var = '_category';
                break;
            default:
                // other params are handled by the parent
                break;
        }
        return parent::__isset($var);
    }

    /**
     * @param string $var
     */
    protected function __unset($var)
    {
        switch ($var) {
            case 'query':
                $var = 'bq';
                break;
            case 'category':
                $var = '_category';
                break;
            default:
                // other params are handled by the parent
                break;
        }
        return parent::__unset($var);
    }

}

