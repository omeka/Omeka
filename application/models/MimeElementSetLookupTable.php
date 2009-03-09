<?php
/**
 * This is only necessary because the table name for MimeElementSetLookup does
 *  not adhere to the pluralization conventions. If it ever does, this class
 *  should be removed.
 *
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class MimeElementSetLookupTable extends Omeka_Db_Table
{
    protected $_name = 'mime_element_set_lookup';
}
