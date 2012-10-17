<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Interface for creating different installers for Omeka.
 * 
 * @package Omeka\Install
 */
interface Installer_InstallerInterface
{    
    public function __construct(Omeka_Db $db);
    public function install();
    public function isInstalled();
}
