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
class Zend_Service_Yahoo_NewsResult extends Zend_Service_Yahoo_Result {
    /**
     * @var string $Summary Sumamry text associated with the result article
     */
    public $Summary;
    
    /**
     * @var string $NewsSource the company who distributed the article
     */
    
    public $NewsSource;
    /**
     *  @var string $NewsSourceUrl the URL for the company who distributed the article
     */
    public $NewsSourceUrl;

    /**
     * @var string $Language the language the article is in
     */
    public $Language;
    
    /**
     * @var string $PublishDate the date the article was published (in unix timestamp format)
     */
    public $PublishDate;
    
    /**
     * @var string $ModificationDate the date the article was modified (in unix timestamp format)
     */
    public $ModificationDate;
    
    /**
     * @var Zend_Service_Yahoo_Image $Thumbnail the thubmnail image for the article, if it exists
     */
    public $Thumbnail;

    /**
     * @todo docblock
     */
    protected $_namespace = "urn:yahoo:yn";


    /**
     * @todo docblock
     */
    public function __construct(DomElement $result) {
        $this->_fields = array('Summary','NewsSource','NewsSourceUrl','Language','PublishDate',
                        'ModificationDate','Thumbnail');
        parent::__construct($result);

        $this->setThumbnail();
    }
}
