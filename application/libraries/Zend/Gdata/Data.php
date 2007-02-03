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
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Data
{
    const GDATA_NAMESPACE_URI = 'http://schemas.google.com/g/2005';

    const ATTENDEE_STATUS_ACCEPTED     = 'accepted';
    const ATTENDEE_STATUS_DECLINED     = 'declined';
    const ATTENDEE_STATUS_INVITED      = 'invited';
    const ATTENDEE_STATUS_TENTATIVE    = 'tentative';

    const ATTENDEE_TYPE_OPTIONAL       = 'optional';
    const ATTENDEE_TYPE_REQUIRED       = 'required';

    const COMMENTS_REGULAR             = 'regular';
    const COMMENTS_REVIEWS             = 'reviews';

    const EVENT_STATUS_CANCELED        = 'canceled';
    const EVENT_STATUS_CONFIRMED       = 'confirmed';
    const EVENT_STATUS_TENTATIVE       = 'tentative';

    const LINK_ALTERNATE               = 'alternate';
    const LINK_ENCLOSURE               = 'enclosure';
    const LINK_ONLINE_LOCATION         = 'onlineLocation';
    const LINK_RELATED                 = 'related';
    const LINK_SELF                    = 'self';
    const LINK_VIA                     = 'via';

    const ORDERBY_MODIFICATION_TIME    = 'modification-time';
    const ORDERBY_NAME                 = 'name';
    const ORDERBY_RELEVANCY            = 'relevancy';
    const ORDERBY_STARTTIME            = 'starttime';

    const PHONE_CAR                    = 'car';
    const PHONE_FAX                    = 'fax';
    const PHONE_GENERAL                = 'general';
    const PHONE_HOME                   = 'home';
    const PHONE_INTERNAL_EXTENSION     = 'internal-extension';
    const PHONE_MOBILE                 = 'mobile';
    const PHONE_OTHER                  = 'other';
    const PHONE_PAGER                  = 'pager';
    const PHONE_SATELLITE              = 'satellite';
    const PHONE_VOIP                   = 'voip';
    const PHONE_WORK                   = 'work';

    const PROJ_ATTENDEES_ONLY          = 'attendees-only';
    const PROJ_BASIC                   = 'basic';
    const PROJ_COMPOSITE               = 'composite';
    const PROJ_FREE_BUSY               = 'free-busy';
    const PROJ_FULL                    = 'full';
    const PROJ_FULL_NOATTENDEES        = 'full-noattendees';

    const RATING_OVERALL               = 'overall';
    const RATING_PRICE                 = 'price';
    const RATING_QUALITY               = 'quality';

    const STATUS_CANCELED              = 'canceled';
    const STATUS_CONFIRMED             = 'confirmed';
    const STATUS_TENTATIVE             = 'tentative';

    const TRANSP_OPAQUE                = 'opaque';
    const TRANSP_TRANSPARENT           = 'transparent';

    const VIS_CONFIDENTIAL             = 'confidential';
    const VIS_DEFAULT                  = 'default';
    const VIS_PRIVATE                  = 'private';
    const VIS_PRIVATE_MAGIC_COOKIE     = 'private-';
    const VIS_PUBLIC                   = 'public';

    const WHERE_ALTERNATE              = 'alternate';
    const WHERE_PARKING                = 'parking';

    const WHO_ATTENDEE                 = 'attendee';
    const WHO_BCC                      = 'bcc';
    const WHO_CC                       = 'cc';
    const WHO_FROM                     = 'from';
    const WHO_ORGANIZER                = 'organizer';
    const WHO_PERFORMER                = 'performer';
    const WHO_REPLY_TO                 = 'reply-to';
    const WHO_SPEAKER                  = 'speaker';
    const WHO_TO                       = 'to';

    /**
     *
     */
    protected static $supportedValues = array(
        'attendeeStatus' => array(
            self::ATTENDEE_STATUS_ACCEPTED,
            self::ATTENDEE_STATUS_DECLINED,
            self::ATTENDEE_STATUS_INVITED,
            self::ATTENDEE_STATUS_TENTATIVE
        ),
        'attendeeType' => array(
            self::ATTENDEE_TYPE_OPTIONAL,
            self::ATTENDEE_TYPE_REQUIRED
        ),
        'comments' => array(
            self::COMMENTS_REGULAR,
            self::COMMENTS_REVIEWS
        ),
        'eventStatus' => array(
            self::EVENT_STATUS_CANCELED,
            self::EVENT_STATUS_CONFIRMED,
            self::EVENT_STATUS_TENTATIVE
        ),
        'link' => array(
            self::LINK_ALTERNATE,
            self::LINK_ENCLOSURE,
            self::LINK_RELATED,
            self::LINK_SELF,
            self::LINK_VIA
        ),
        'link#gdata' => array(
            self::LINK_ONLINE_LOCATION
        ),
        'orderby#base' => array(
            self::ORDERBY_MODIFICATION_TIME,
            self::ORDERBY_NAME,
            self::ORDERBY_RELEVANCY
        ),
        'orderby#calendar' => array(
            self::ORDERBY_STARTTIME
        ),
        'phoneNumber' => array(
            self::PHONE_CAR,
            self::PHONE_FAX,
            self::PHONE_GENERAL,
            self::PHONE_HOME,
            self::PHONE_INTERNAL_EXTENSION,
            self::PHONE_MOBILE,
            self::PHONE_OTHER,
            self::PHONE_PAGER,
            self::PHONE_SATELLITE,
            self::PHONE_VOIP,
            self::PHONE_WORK
        ),
        'projection' => array(
            self::PROJ_ATTENDEES_ONLY,
            self::PROJ_BASIC,
            self::PROJ_COMPOSITE,
            self::PROJ_FREE_BUSY,
            self::PROJ_FULL,
            self::PROJ_FULL_NOATTENDEES
        ),
        'rating' => array(
            self::RATING_OVERALL,
            self::RATING_PRICE,
            self::RATING_QUALITY
        ),
        'status' => array(
            self::STATUS_CANCELED,
            self::STATUS_CONFIRMED,
            self::STATUS_TENTATIVE
        ),
        'transparency' => array(
            self::TRANSP_OPAQUE,
            self::TRANSP_TRANSPARENT
        ),
        'visibility' => array(
            self::VIS_CONFIDENTIAL,
            self::VIS_DEFAULT,
            self::VIS_PRIVATE,
            self::VIS_PRIVATE_MAGIC_COOKIE,
            self::VIS_PUBLIC
        ),
        'where' => array(
            '',
            self::WHERE_ALTERNATE,
            self::WHERE_PARKING
        ),
        'who#event' => array(
            self::WHO_ATTENDEE,
            self::WHO_PERFORMER,
            self::WHO_ORGANIZER,
            self::WHO_SPEAKER
        ),
        'who#message' => array(
            self::WHO_BCC,
            self::WHO_CC,
            self::WHO_FROM,
            self::WHO_REPLY_TO,
            self::WHO_TO
        )
    );

    /**
     *
     * @param string $value
     * @param string $key
     */
    public static function isValid($value, $key)
    {
        if (!array_key_exists($key, self::$supportedValues)) {
            return false;
        }
        if (in_array($value, self::$supportedValues[$key])) {
            return true;
        }
        switch ($key) {
            case 'visibility':
                if (!strncmp($value, self::VIS_PRIVATE_MAGIC_COOKIE, strlen(self::VIS_PRIVATE_MAGIC_COOKIE))) {
                    return true;
                }
                break;
        }
        return false;
    }

    /**
     *
     * @param string $key
     * @return array values
     */
    public static function getValues($key)
    {
        if (!array_key_exists($key, self::$supportedValues)) {
            return false;
        }
        return self::$supportedValues[$key];
    }

}

