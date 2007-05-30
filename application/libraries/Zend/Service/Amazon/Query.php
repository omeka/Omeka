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
 * @subpackage Amazon
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Zend_Service_Amazon
 */
require_once 'Zend/Service/Amazon.php';


/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Amazon
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Amazon_Query extends Zend_Service_Amazon
{
	private $_search = array();
	private $_searchIndex = null;

	function __call($method, $args)
	{
	    /**
	     * @todo revisit this - also add some bounds checking for $args
	     */

		if (strtolower($method) == 'asin') {
			$this->_searchIndex = 'asin';
			$this->_search['itemId'] = $args[0];
			return $this;
		}

		if (strtolower($method) == 'category') {
			if (isset(self::$_searchParams[$args[0]])) {
			    $this->_searchIndex = $args[0];
				$this->_search['SearchIndex'] = $args[0];
			} else {
				throw new Zend_Service_Exception('Unknown Search Category');
			}
		} else if ($this->_search['SearchIndex'] !== null || $this->_searchIndex !== null || $this->_searchIndex == 'asin') {
			$this->_search[$method] = $args[0];
		} else {
			throw new Zend_Service_Exception('You must set a category before setting the search parameters');
		}

		return $this;
	}

    /**
     * Search using the prepared query
     *
     * @return Zend_Service_Amazon_Item|Zend_Service_Amazon_ResultSet
     */
	function search()
	{
		if ($this->_searchIndex == 'asin') {
			return $this->itemLookup($this->_searchIndex['itemId'], $this->_search);
		}
		return $this->itemSearch($this->_search);
	}
}

