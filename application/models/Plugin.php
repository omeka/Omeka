<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @subpackage Models
 * @author CHNM
 **/
class Plugin extends Omeka_Record
{
    public $name;
    public $active = '0';
    public $version;
        
    protected function _validate()
    {
        if (empty($this->name)) {
            $this->addError('name', 'Names of plugins must not be blank');
        }
    }
}