<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * ElementText
 *
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class ElementText extends Omeka_Record
{
    public $record_id;
    public $record_type_id;
    public $element_id;
    public $html = 0;
    public $text;
    
    public function __toString()
    {
        return (string) $text;
    }    
    
    public function setText($text)
    {
        $this->text = (string) $text;
    }
    
    public function getText()
    {
        return (string) $this->text;
    }
    
    public function isHtml()
    {
        return (boolean) $this->html;
    }
}
