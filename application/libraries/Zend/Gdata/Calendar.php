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
require_once('Zend/Gdata.php');

/**
 * Zend_Gdata_Data
 */
require_once('Zend/Gdata/Data.php');

/**
 * Zend_Gdata_InvalidArgumentException
 */
require_once('Zend/Gdata/InvalidArgumentException.php');

/**
 * Gdata Calendar
 *
 * @link http://code.google.com/apis/gdata/calendar.html
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Calendar extends Zend_Gdata
{
    const CALENDAR_FEED_URI = 'http://www.google.com/calendar/feeds';
    const CALENDAR_POST_URI = 'http://www.google.com/calendar/feeds/default/private/full';

    protected $_defaultPostUri = self::CALENDAR_POST_URI;

    /**
     * Create Gdata_Calendar object
     */
    public function __construct($client = null)
    {
        parent::__construct($client);
        $this->_httpClient->setParameterPost('service', 'cl');
    }

    /**
     * Retreive feed object
     *
     * @return Zend_Feed
     */
    public function getCalendarFeed($uri = null)
    {
        if ($uri == null) {
            $uri = self::CALENDAR_FEED_URI;
        }
        if (isset($this->_params['_user'])) {
            $uri .= '/' . $this->_params['_user'];
        } else {
            $uri .= '/default';
        }
        if (isset($this->_params['_visibility'])) {
            $uri .= '/' . $this->_params['_visibility'];
        } else {
            $uri .= '/public';
        }
        if (isset($this->_params['_projection'])) {
            $uri .= '/' . $this->_params['_projection'];
        } else {
            $uri .= '/full';
        }
        if (isset($this->_params['_event'])) {
            $uri .= '/' . $this->_params['_event'];
            if (isset($this->_params['_comments'])) {
                $uri .= '/comments/' . $this->_params['_comments'];
            }
        }

        $uri .= $this->getQueryString();
        return parent::getFeed($uri);
    }

    /**
     * Retrieve feed object
     *
     * @return Zend_Feed
     */
    public function getCalendarListFeed()
    {
        $uri = self::CALENDAR_FEED_URI;
        if (isset($this->_params['_user'])) {
            $uri .= '/' . $this->_params['_user'];
        } else {
            $uri .= '/default';
        }
        return parent::getFeed($uri);
    }

    /**
     * @param string $value
     */
    public function setComments($value)
    {
        $this->comments = $value;
    }

    /**
     * @param string $value
     */
    public function setEvent($value)
    {
        $this->event = $value;
    }

    /**
     * @param string $value
     */
    public function setStartMax($value)
    {
        $this->startMax = $value;
    }

    /**
     * @param string $value
     */
    public function setStartMin($value)
    {
        $this->startMin = $value;
    }

    /**
     * @param string $value
     */
    public function setOrderby($value)
    {
        $this->orderby = $value;
    }

    /**
     * @param string $value
     */
    public function setProjection($value)
    {
        $this->projection = $value;
    }

    /**
     * @param string $value
     */
    public function setUser($value)
    {
        $this->user = $value;
    }

    /**
     * @return string visibility
     */
    public function setVisibility($value)
    {
        $this->visibility = $value;
    }

    /**
     * @return string comments
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @return string event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return string startMax
     */
    public function getStartMax()
    {
        return $this->startMax;
    }

    /**
     * @return string startMin
     */
    public function getStartMin()
    {
        return $this->startMin;
    }

    /**
     * @return string orderBy
     */
    public function getOrderby()
    {
        return $this->orderby;
    }

    /**
     * @return string projection
     */
    public function getProjection()
    {
        return $this->projection;
    }

    /**
     * @return string user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string visibility
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * @param string $var
     * @param string $value
     * @throws Zend_Gdata_InvalidArgumentException
     */
    protected function __set($var, $value)
    {
        switch ($var) {
            case 'startMin':
                $var = 'start-min';
                $value = $this->formatTimestamp($value);
                break;
            case 'startMax':
                $var = 'start-max';
                $value = $this->formatTimestamp($value);
                break;
            case 'visibility':
            case 'projection':
                if (!Zend_Gdata_Data::isValid($value, $var)) {
                    throw new Zend_Gdata_InvalidArgumentException("Unsupported $var value: '$value'");
                }
                $var = "_$var";
                break;
            case 'orderby':
                if (!Zend_Gdata_Data::isValid($value, 'orderby#calendar')) {
                    throw new Zend_Gdata_InvalidArgumentException("Unsupported $var value: '$value'");
                }
                break;
            case 'user':
                $var = '_user';
                // @todo: validate user value
                break;
            case 'event':
                $var = '_event';
                // @todo: validate event value
                break;
            case 'comments':
                $var = '_comments';
                // @todo: validate comments subfeed value
                break;
            default:
                // other params are handled by parent
                break;
        }
        parent::__set($var, $value);
    }

    protected function __get($var)
    {
        switch ($var) {
            case 'startMin':
                $var = 'start-min';
                break;
            case 'startMax':
                $var = 'start-max';
                break;
            case 'visibility':
                $var = '_visibility';
                break;
            case 'projection':
                $var = '_projection';
                break;
            case 'user':
                $var = '_user';
                break;
            case 'event':
                $var = '_event';
                break;
            case 'comments':
                $var = '_comments';
                break;
            default:
                break;
        }
        return parent::__get($var);
    }

    protected function __isset($var)
    {
        switch ($var) {
            case 'startMin':
                $var = 'start-min';
                break;
            case 'startMax':
                $var = 'start-max';
                break;
            case 'visibility':
                $var = '_visibility';
                break;
            case 'projection':
                $var = '_projection';
                break;
            case 'user':
                $var = '_user';
                break;
            case 'event':
                $var = '_event';
                break;
            case 'comments':
                $var = '_comments';
                break;
            default:
                break;
        }
        return parent::__isset($var);
    }

    protected function __unset($var)
    {
        switch ($var) {
            case 'startMin':
                $var = 'start-min';
                break;
            case 'startMax':
                $var = 'start-max';
                break;
            case 'visibility':
                $var = '_visibility';
                break;
            case 'projection':
                $var = '_projection';
                break;
            case 'user':
                $var = '_user';
                break;
            case 'event':
                $var = '_event';
                break;
            case 'comments':
                $var = '_comments';
                break;
            default:
                break;
        }
        return parent::__unset($var);
    }

}

