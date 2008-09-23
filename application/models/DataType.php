<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

require_once 'DataTypeTable.php';

/**
 * DataType
 *
 * @subpackage Models
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class DataType extends Omeka_Record
{
    public $name;
    public $description;
}