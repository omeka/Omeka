<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Plugin extends Omeka_Record
{
    public $name;
    public $active = '0';
        
    protected function _validate()
    {
        if (empty($this->name)) {
            $this->addError('name', 'Names of plugins must not be blank');
        }
    }
}