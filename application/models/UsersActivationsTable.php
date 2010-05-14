<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
 **/
class UsersActivationsTable extends Omeka_Db_Table
{
    
    public function findByUrl($url)
    {
        return $this->fetchObject($this->getSelect()->where('url = ?', $url)->limit(1));
    }
}
