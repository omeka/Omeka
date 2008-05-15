<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/
 
/**
 * @todo Remove this class when we get rid of the file_meta_lookup table.  
 * This class only exists because the name of the table is not inflected 
 * properly with the name of the model.
 *
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class FileMetaLookupTable extends Omeka_Db_Table
{
    protected $_name = 'file_meta_lookup';
}
