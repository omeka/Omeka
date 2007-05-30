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
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Amazon
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Amazon_Image
{
	public $Url;
	public $Height;
	public $Width;

    /**
     * Parse a given Image element
     *
     * @param DomElement $dom
     */
    public function __construct(DomElement $dom)
    {
    	$xpath = new DOMXPath($dom->ownerDocument);
    	$xpath->registerNamespace('az', 'http://webservices.amazon.com/AWSECommerceService/2005-10-05');
        $this->Url = Zend_Uri::factory($xpath->query('./az:URL/text()', $dom)->item(0)->data);
        $this->Height = (int) $xpath->query('./az:Height/text()', $dom)->item(0)->data;
        $this->Width = (int) $xpath->query('./az:Width/text()', $dom)->item(0)->data;
    }
}
