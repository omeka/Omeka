<?php
require_once 'Download.php';

/**
 * @version $Id$
 * @copyright Scand Ltd.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @author Scand Ltd.
 **/

class DownloadLogger
{
    public function __construct()
    {
    }
    
    /**
     * Get File
     * @return File
     */
    public function getFile($file_name)
    {
        $file = new File($this->getDb());
        $table = $file->getTable('File');
        $select = $table->getSelect()->where(" f.archive_filename = ? ", $file_name)->order('f.id ASC');
        $res = $table->fetchObjects($select, array());
        if (count($res)==0)
            return NULL;
        return $res[0];
    }
    
    /**
     * Log file download 
     * @param File $file
     * @param unknown_type $remote_ip
     * @param unknown_type $remote_user
     * @return Download or NULL
     */
    public function log(File $file, $remote_ip = '', $remote_user = '')
    {
        $dl = new Download();
        $dl
            ->set_item_id($file->item_id)
            ->set_guest_ip($remote_ip)
            ->set_guest_name($remote_user);
        if ($dl->save())
            return $dl;
        return NULL;
    }
    
    
    public function getDb()
    {
        $core = new Omeka_Core;
        $core->phasedLoading('initializeDb');
        return $core->getDb();
    }
}