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
 * @package    Zend_Feed
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Zend_Feed_Abstract
 */
require_once 'Zend/Feed/Abstract.php';

/**
 * Zend_Feed_EntryRss
 */
require_once 'Zend/Feed/EntryRss.php';


/**
 * RSS channel class
 *
 * The Zend_Feed_Rss class is a concrete subclass of
 * Zend_Feed_Abstract meant for representing RSS channels. It does not
 * add any methods to its parent, just provides a classname to check
 * against with the instanceof operator, and expects to be handling
 * RSS-formatted data instead of Atom.
 *
 * @category   Zend
 * @package    Zend_Feed
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Feed_Rss extends Zend_Feed_Abstract
{
    /**
     * The classname for individual channel elements.
     *
     * @var string
     */
    protected $_entryClassName = 'Zend_Feed_EntryRss';

    /**
     * The element name for individual channel elements (RSS <item>s).
     *
     * @var string
     */
    protected $_entryElementName = 'item';

    /**
     * The default namespace for RSS channels.
     *
     * @var string
     */
    protected $_defaultNamespace = 'rss';


    /**
     * Override Zend_Feed_Abstract to set up the $_element and $_entries aliases.
     */
    public function __wakeup()
    {
        parent::__wakeup();

        // Find the base channel element and create an alias to it.
        $this->_element = $this->_element->getElementsByTagName('channel')->item(0);
        if (!$this->_element) {
            throw new Zend_Feed_Exception('No root <channel> element found, cannot parse channel.');
        }

        // Find the entries and save a pointer to them for speed and
        // simplicity.
        $this->_buildEntryCache();
    }


    /**
     * Make accessing some individual elements of the channel easier.
     *
     * Special accessors 'item' and 'items' are provided so that if
     * you wish to iterate over an RSS channel's items, you can do so
     * using foreach ($channel->items as $item) or foreach
     * ($channel->item as $item).
     *
     * @param string $var The property to access.
     * @return mixed
     */
    public function __get($var)
    {
        switch ($var) {
            case 'item':
                // fall through to the next case
            case 'items':
                return $this;

            default:
                return parent::__get($var);
        }
    }

}
