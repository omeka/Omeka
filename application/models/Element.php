<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

require_once 'ElementTable.php';
require_once 'RecordType.php';
require_once 'DataType.php';
require_once 'ElementSet.php';
 
/**
 * 
 *
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Element extends Omeka_Record
{
    public $record_type_id;
    public $data_type_id;
    public $element_set_id;
    public $order;
    public $name = '';
    public $description;

    protected function _validate()
    {
        if (empty($this->name)) {
            $this->addError('name', 'Name must not be empty!');
        }
        
        if (empty($this->data_type_id)) {
            $this->addError('data_type_id', 'Element must have a valid data type!');
        }
        
        if (empty($this->record_type_id)) {
            $this->addError('record_type_id', 'Element must have a valid record type!');
        }
    }
}
