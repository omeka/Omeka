<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

require_once 'Type.php';
require_once 'Metafield.php';

/**
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class TypesMetafields extends Omeka_Record
{
    public $type_id;
    public $metafield_id;
    public $plugin_id;
    
    protected function _validate()
    {
        if (empty($this->type_id)) {
            $this->addError('type_id', 'Metafield must be related to a type');
        }
        
        if (empty($this->metafield_id)) {
            $this->addError('metafield_id', 'Type must be related to a metafield');
        }
    }
}