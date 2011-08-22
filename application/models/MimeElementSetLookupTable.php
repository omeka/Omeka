<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * This is only necessary because the table name for MimeElementSetLookup does
 *  not adhere to the pluralization conventions. If it ever does, this class
 *  should be removed.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class MimeElementSetLookupTable extends Omeka_Db_Table
{
    protected $_name = 'mime_element_set_lookup';
}
