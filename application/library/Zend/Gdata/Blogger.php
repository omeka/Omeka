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
 * Gdata Blogger
 *
 * @link http://code.google.com/apis/gdata/blogger.html
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Blogger extends Zend_Gdata
{
    const BLOGGER_LIST_URI = 'http://www.blogger.com/feeds/default/blogs';

    /**
     * @param string blogname
     * @return mixed feed
     * @throws Zend_Gdata_InvalidArgumentException
     */
    public function getBloggerFeed($blogName = null)
    {
        if ($blogName != null) {
            $this->blogName = $blogName;
        }
        if (!isset($this->blogName)) {
            throw new Zend_Gdata_InvalidArgumentException('You must specify a blog name.');
        }
        $uri = "http://{$this->blogName}.blogspot.com/feeds/posts/default";
        $uri .= $this->getQueryString();

        /**
         * Category and Entry queries are not currently (12/2006)
         * supported in the Google Blogger Data API.
         */

        return parent::getFeed($uri);
    }

    /**
     * @return string blogname
     */
    public function getBloggerListFeed()
    {
        $uri = self::BLOGGER_LIST_URI;
        return parent::getFeed($uri);
    }

    /**
     * POST xml data to Google with authorization headers set
     *
     * @param string $xml
     * @return Zend_Http_Response
     * @throws Zend_Gdata_InvalidArgumentException
     */
    public function post($xml, $uri = null)
    {
        if (!isset($this->blogName)) {
            throw new Zend_Gdata_InvalidArgumentException('You must specify a blog name.');
        }
        if ($uri == null) {
            $uri = "http://www.blogger.com/feeds/{$this->blogName}/posts/default";
        }
        return parent::post($xml, $uri);
    }

    /**
     * @return string blogname
     */
    public function getBlogName()
    {
        return $this->blogName;
    }

    /**
     * @return int publishedMax
     */
    public function getPublishedMax()
    {
        return $this->publishedMax;
    }

    /**
     * @return int publishedMin
     */
    public function getPublishedMin()
    {
        return $this->publishedMin;
    }

    /**
     * @param string $value
     */
    public function setBlogName($value)
    {
        $this->blogName = $value;
    }

    /**
     * @param int $value
     */
    public function setPublishedMax($value)
    {
        $this->publishedMax = $value;
    }

    /**
     * @param int $value
     */
    public function setPublishedMin($value)
    {
        $this->publishedMin = $value;
    }

    /**
     * @param string $var
     * @param string $value
     * @throws Zend_Gdata_InvalidArgumentException
     */
    protected function __set($var, $value)
    {
        switch ($var) {
            case 'query':
            case 'q':
                $var = 'q';
                throw new Zend_Gdata_InvalidArgumentException('Text queries are not currently supported in Blogger.');
                break;
            case 'publishedMin':
                $var = 'published-min';
                $value = $this->formatTimestamp($value);
                break;
            case 'publishedMax':
                $var = 'published-max';
                $value = $this->formatTimestamp($value);
                break;
            case 'blogName':
                $var = '_blogName';
                break;
            case 'category':
                $var = '_category';
                throw new Zend_Gdata_InvalidArgumentException('Category queries are not currently supported in Blogger.');
                break;
            case 'entry':
                $var = '_entry';
                throw new Zend_Gdata_InvalidArgumentException('Entry queries are not currently supported in Blogger.');
                break;
            default:
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
            case 'publishedMin':
                $var = 'published-min';
                break;
            case 'publishedMax':
                $var = 'published-max';
                break;
            case 'blogName':
                $var = '_blogName';
                break;
            default:
                break;
        }
        return parent::__get($var);
    }

    /**
     * @param string $var
     * @return bool isset
     */
    protected function __isset($var)
    {
        switch ($var) {
            case 'publishedMin':
                $var = 'published-min';
                break;
            case 'publishedMax':
                $var = 'published-max';
                break;
            case 'blogName':
                $var = '_blogName';
                break;
            default:
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
            case 'publishedMin':
                $var = 'published-min';
                break;
            case 'publishedMax':
                $var = 'published-max';
                break;
            case 'blogName':
                $var = '_blogName';
                break;
            default:
                break;
        }
        return parent::__unset($var);
    }

}
