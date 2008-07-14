<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

require_once 'ElementTextTable.php';

/**
 * ElementText
 *
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class ElementText extends Omeka_Record
{
    public $record_id;
    public $record_type_id;
    public $element_id;
    public $html;
    public $text;    
}
