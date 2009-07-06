<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_Test
 **/

class Omeka_Test_Resource_Search extends Zend_Application_Resource_ResourceAbstract
{    
    protected $_search;
    
    public function init()
    {   
        return Omeka_Search::getInstance(ARCHIVE_DIR . DIRECTORY_SEPARATOR . 'test_lucene_index');
    }
}