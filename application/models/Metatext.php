<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

require_once 'Item.php';
require_once 'Metafield.php';
require_once 'MetatextTable.php';

/**
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Metatext extends Omeka_Record { 
    
	public $item_id;
	public $metafield_id;
	public $text;

	public function __toString() {
		return (string) $this->text;
	}
}