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
 * @subpackage Yahoo
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * @todo coding standards: naming of instance variables
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Yahoo
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Yahoo_WebResult extends Zend_Service_Yahoo_Result {
    /**
     * @var string $Summary a summary of the result
     */
    public $Summary;
    
    /**
     * @var string MimeType the file type of the result (text, html, pdf, etc.)
     */
    public $MimeType;
    
    /**
     * @var string $ModificationDate the modification time of the result (as a unix timestamp)
     */
    public $ModificationDate;
    
    /**
     * @var string $CacheUrl the URL for the Yahoo cache of this page, if it exists
     */
    public $CacheUrl;
    
    /**
     * @var int $CacheSize the size of the cache entry
     */
    public $CacheSize;

    /**
     * @todo docblock
     */
    protected $_namespace = "urn:yahoo:srch";

    
    /**
     * @todo dockblock
     */
    public function __construct(DomElement $result) {
        $this->_fields = array('Summary','MimeType','ModificationDate');
        parent::__construct($result);

        $this->_xpath = new DOMXPath($result->ownerDocument);
    	$this->_xpath->registerNamespace("yh", $this->_namespace);

        $this->CacheUrl = (string) $this->_xpath->query("//yh:Cache/yh:Url/text()")->item(0)->data;
        $this->CacheSize = (string) $this->_xpath->query("//yh:Cache/yh:Size/text()")->item(0)->data;
    }
}
