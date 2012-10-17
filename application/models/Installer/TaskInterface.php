<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Interface for Installer tasks.
 * 
 * @package Omeka\Install
 */
interface Installer_TaskInterface
{
    public function install(Omeka_Db $db);
}
