<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

require_once 'MimeElementSetLookupTable.php';

/**
 * MimeElementSetLookup
 *
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class MimeElementSetLookup extends Omeka_Record
{
    public $element_set_id;
    public $mime;
}