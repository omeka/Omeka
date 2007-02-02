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
 * Zend_Service_Flickr_Result
 */
require_once 'Zend/Service/Flickr/Result.php';


/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Flickr
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Flickr_ResultSet implements SeekableIterator
{
    /**
     * @var array $Result an array of XNC_Services_Flickr_GenericSearchResult results
     */
    protected $_results;

    /**
     * @var int $totalResultsAvailable the total number of results available
     */
    public $totalResultsAvailable;

    /*
     * @var int $totalResultsReturned the number of results in this result set
     */
    public $totalResultsReturned;

    /*
     * @var int $firstResultPosition the offset in the total result set of this search set
     */
    public $firstResultPosition;

    /**
     * @var Zend_Service_Flickr $_flickr Original Zend_Service_Flickr object with which the request was made
     */
    private $_flickr;

    /**
     * @var int How many items Per Page
     */
    private $_perPage;

    /**
     * @var int Current Item for the Iterator
     */
    private $_currentItem = 0;

    /**
     * Parse the Flickr Result Set
     *
     * @param DomDocument $dom
     * @param Zend_Service_Flickr $flickr
     */
    public function __construct(DomDocument $dom, Zend_Service_Flickr $flickr)
    {
    	$this->_flickr = $flickr;

    	$xpath = new DOMXPath($dom);

    	$photos = $xpath->query('//photos')->item(0);

    	$page    = $photos->getAttribute('page');
    	$pages   = $photos->getAttribute('pages');
    	$perPage = $photos->getAttribute('perpage');
    	$total   = $photos->getAttribute('total');

		$this->totalResultsReturned  = ($page == $pages) ? ($total - ($page - 1) * $perPage) : $perPage;
        $this->firstResultPosition   = ($page - 1) * $perPage + 1;
        $this->totalResultsAvailable = $total;

        if ($total > 0) {
            $this->_results = $xpath->query('//photo');
        }
    }

    /**
     * Total Number of results returned
     *
     * @return int Total number of results returned
     */
    public function totalResults()
    {
		return (int) $this->totalResultsReturned;
    }

    /**
     * Return the Current Item
     *
     * @return Zend_Service_Flickr_Result
     */
    public function current()
    {
    	return new Zend_Service_Flickr_Result($this->_results->item($this->_currentItem), $this->_flickr);
    }

    /**
     * Implement SeekableIterator::key
     *
     * @return int
     */
    public function key()
    {
    	return $this->_currentItem;
    }

    /**
     * Implement SeekableIterator::next
     */
    public function next()
    {
    	$this->_currentItem += 1;
    }

    /**
     * Implement SeekableIterator::rewind
     *
     * @return boolean
     */
    public function rewind()
    {
    	$this->_currentItem = 0;
    	return true;
    }

    /**
     * Implement SeekableIterator::seek
     *
     * @param integer $item
     * @return Zend_Service_Flickr_Result
     * @throws Zend_Service_Exception
     */
    public function seek($item)
    {
    	if ($this->valid($item)) {
    		$this->_currentItem = $item;
    		return $this->current();
    	} else {
    		/* @todo Should be an OutOfBoundsException but that was added in PHP 5.1 */
    		throw new Zend_Service_Exception('Item not found');
    	}
    }

    /**
     * Implement SeekableIterator::valid
     *
     * @param int $item
     * @return boolean
     */
    public function valid($item = null)
    {
    	if (empty($item) && empty($this->_results)) {
    		return false;
    	} elseif (empty($item) && $this->_currentItem < $this->_results->length) {
    		return true;
    	} else if (isset($item) && $item <= $this->_results->length) {
    		return true;
    	} else {
    		return false;
    	}
    }
}

