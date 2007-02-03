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
 * Zend_Gdata_BadMethodCallException
 */
require_once 'Zend/Gdata/BadMethodCallException.php';

/**
 * Zend_Gdata_InvalidArgumentException
 */
require_once 'Zend/Gdata/InvalidArgumentException.php';

/**
 * Gdata CodeSearch
 *
 * @link http://code.google.com/apis/gdata/codesearch.html
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_CodeSearch extends Zend_Gdata
{
    const CODESEARCH_FEED_URI = 'http://www.google.com/codesearch/feeds/search';

    /**
     * Retreive feed object
     *
     * @return Zend_Feed
     */
    public function getCodeSearchFeed($uri = null)
    {
        if ($uri == null) {
            $uri = self::CODESEARCH_FEED_URI;
        }
        $uri .= $this->getQueryString();
        return parent::getFeed($uri);
    }

    /**
     * There are no POST operations for CodeSearch.
     *
     * @param string $xml
     * @param string $uri 
     * @throws Zend_Gdata_BadMethodCallException
     */
    public function post($xml, $uri = null)
    {
        throw new Zend_Gdata_BadMethodCallException('There are no post operations for CodeSearch.');
    }

    /**
     * @param string $var
     * @param string $value
     * @throws Zend_Gdata_InvalidArgumentException
     */
    protected function __set($var, $value)
    {
        switch ($var) {
            case 'updatedMin':
            case 'updatedMax':
                throw new Zend_Gdata_InvalidArgumentException("Parameter '$var' is not currently supported in CodeSearch.");
                break;
        }
        parent::__set($var, $value);
    }

}
