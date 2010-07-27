<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Interface for Installer tasks.
 * 
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
 */
interface Installer_TaskInterface
{
    public function install(Omeka_Db $db);
}
