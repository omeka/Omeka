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
class Zend_Service_Yahoo_ImageResult extends Zend_Service_Yahoo_Result {
    /**
     * @var string $Summary  summary info for the image
     */
    public $Summary;
    
    /**
     * @var string $RefererUrl the URL of the webpage hosting the image
     */
    public $RefererUrl;
    
    /**
     * @var int $FileSize the size of the files in bytes
     */
    public  $FileSize;
    
    /**
     * @var string $FileFormat  the type of file (bmp,gif,jpeg, etc.)
     */
    public $FileFormat;
    
    /**
     * @var int $Height the height of the image in pixels
     */
    public $Height;
    
    /**
     * @var int $Width the width of the image in pixels
     */
    public $Width;

    /**
     * @var Zend_Service_Yahoo_Image $Thumbnail the thubmnail image for the article, if it exists
     */
    public $Thumbnail;
    
    /**
     * @todo docblock
     */
    protected $_namespace = "urn:yahoo:srchmi";


    /**
     * @todo docblock
     */
    public function __construct(DomElement $result) {
        $this->_fields = array('Summary', 'RefererUrl', 'FileSize',
                              'FileFormat', 'Height', 'Width', 'Thumbnail');

        parent::__construct($result);
        $this->setThumbnail();
    }
}
