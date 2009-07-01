<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * A wrapper for Zend_Search_Lucene
 *
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Omeka_Search
{
    static private $_instance;
    
    private $_luceneIndex;
    
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function __construct()
    {
        try {
            $this->_luceneIndex = Zend_Search_Lucene::open(LUCENE_INDEX_DIR);
        } catch (Exception $e) {
            $this->_luceneIndex = Zend_Search_Lucene::create(LUCENE_INDEX_DIR);
        }
    }
    
    public function getLuceneIndex()
    {
        return $this->_luceneIndex;
    }
}