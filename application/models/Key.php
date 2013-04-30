<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Record
 */
class Key extends Omeka_Record_AbstractRecord
{
    public $user_id;
    public $label;
    public $key;
    public $ip;
    public $accessed;
}
