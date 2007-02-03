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
class Zend_Service_Yahoo_Result {
    /**
     * @var string $Title the title of the search entry
     */
    public $Title;
    
    /**
     * @var string $Url the URL of the found object
     */
    public $Url;
    
    /*
     * @var string $ClickUrl the URL for linking to the found object
     */
    public $ClickUrl;

    /**
     * @todo docblock
     */
    protected $_fields;

    /**
     * @todo docblock
     */
    protected $_result;

    /**
     * @todo docblock
     */
    protected $_xpath;

    
    /**
     * @todo remove ning comment
     * The XNC_Services_Yahoo_Search_GenericResult constructor.  This method takes
     * a SimpleXML object representing the ReST response fragment for
     * this found object.
     *
     * @param SimpleXmlElement $sxml the ReST fragment for this object
     * @return XNC_Services_Yahoo_Search_GenericResult the new object
     */
    public function __construct(DomElement $result) {
        // default fields for all search results:
        $fields = array('Title','Url','ClickUrl');

        // merge w/ child's fields
        $this->_fields = array_merge($this->_fields, $fields);

        $this->_xpath = new DOMXPath($result->ownerDocument);
    	$this->_xpath->registerNamespace("yh", $this->_namespace);

        // add search results to appropriate fields

        foreach($this->_fields as $f) {
            $query = "./yh:$f/text()";
            $node = $this->_xpath->query($query, $result);
            if ($node->length == 1) {
                $this->{$f} = $node->item(0)->data;
            }
        }

        $this->_result = $result;
    }

    
    /**
     * @todo docblock
     * @todo naming conventions: protected
     */
    protected function setThumbnail() {
        if (!($this->_xpath instanceof DomXPath)) {
           $this->_xpath = new DOMXPath($this->_result->ownerDocument);
    	   $this->_xpath->registerNamespace("yh", $this->_namespace);
        }
        $node = $this->_xpath->query("./yh:Thumbnail", $this->_result);
        if ($node->length == 1) {
            $this->Thumbnail = new Zend_Service_Yahoo_Image($node->item(0), $this->_namespace);
        } else {
            $this->Thumbnail = null;
        }
    }
}
