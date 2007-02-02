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
 * Zend_Feed_EntryAtom
 */
require_once 'Zend/Feed/EntryAtom.php';


/**
 * Atom feed class
 *
 * The Zend_Feed_Atom class is a concrete subclass of the general
 * Zend_Feed_Abstract class, tailored for representing an Atom
 * feed. It shares all of the same methods with its abstract
 * parent. The distinction is made in the format of data that
 * Zend_Feed_Atom expects, and as a further pointer for users as to
 * what kind of feed object they have been passed.
 *
 * @category   Zend
 * @package    Zend_Feed
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Feed_Atom extends Zend_Feed_Abstract
{

    /**
     * The classname for individual feed elements.
     *
     * @var string
     */
    protected $_entryClassName = 'Zend_Feed_EntryAtom';

    /**
     * The element name for individual feed elements (Atom <entry>
     * elements).
     *
     * @var string
     */
    protected $_entryElementName = 'entry';

    /**
     * The default namespace for Atom feeds.
     *
     * @var string
     */
    protected $_defaultNamespace = 'atom';


    /**
     * Override Zend_Feed_Abstract to set up the $_element and $_entries aliases.
     */
    public function __wakeup()
    {
        parent::__wakeup();

        // Find the base feed element and create an alias to it.
        $element = $this->_element->getElementsByTagName('feed')->item(0);
        if (!$element) {
            // Try to find a single <entry> instead.
            $element = $this->_element->getElementsByTagName($this->_entryElementName)->item(0);
            if (!$element) {
                throw new Zend_Feed_Exception('No root <feed> or <' . $this->_entryElementName
                                              . '> element found, cannot parse feed.');
            }

            $doc = new DOMDocument($this->_element->version,
                                   $this->_element->actualEncoding);
            $feed = $doc->appendChild($doc->createElement('feed'));
            $feed->appendChild($doc->importNode($element, true));
            $element = $feed;
        }

        $this->_element = $element;

        // Find the entries and save a pointer to them for speed and
        // simplicity.
        $this->_buildEntryCache();
    }


    /**
     * Easy access to <link> tags keyed by "rel" attributes.
     *
     * If $elt->link() is called with no arguments, we will attempt to
     * return the value of the <link> tag(s) like all other
     * method-syntax attribute access. If an argument is passed to
     * link(), however, then we will return the "href" value of the
     * first <link> tag that has a "rel" attribute matching $rel:
     *
     * $elt->link(): returns the value of the link tag.
     * $elt->link('self'): returns the href from the first <link rel="self"> in the entry.
     *
     * @param string $rel The "rel" attribute to look for.
     * @return mixed
     */
    public function link($rel = null)
    {
        if ($rel === null) {
            return parent::__call('link', null);
        }

        // index link tags by their "rel" attribute.
        $links = parent::__get('link');
        if (!is_array($links)) {
            if ($links instanceof Zend_Feed_Element) {
                $links = array($links);
            } else {
                return $links;
            }
        }

        foreach ($links as $link) {
            if (empty($link['rel'])) {
                continue;
            }
            if ($rel == $link['rel']) {
                return $link['href'];
            }
        }

        return null;
    }


    /**
     * Make accessing some individual elements of the feed easier.
     *
     * Special accessors 'entry' and 'entries' are provided so that if
     * you wish to iterate over an Atom feed's entries, you can do so
     * using foreach ($feed->entries as $entry) or foreach
     * ($feed->entry as $entry).
     *
     * @param string $var The property to access.
     * @return mixed
     */
    public function __get($var)
    {
        switch ($var) {
            case 'entry':
                // fall through to the next case
            case 'entries':
                return $this;

            default:
                return parent::__get($var);
        }
    }

}
