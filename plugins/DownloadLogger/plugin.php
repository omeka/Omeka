<?php
add_plugin_hook('install', 'download_install');
add_plugin_hook('uninstall', 'download_uninstall');
add_plugin_hook('config', 'download_config');
add_plugin_hook('config_form', 'download_config_form');

add_plugin_hook('admin_append_to_items_show_primary', 'download_admin_append_to_items_show');

function download_install()
{
    $db = get_db();
	$db->query("
CREATE TABLE IF NOT EXISTS `{$db->prefix}download` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `item_id` int(10) unsigned NOT NULL,
  `guest_name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `guest_ip` varchar(255) collate utf8_unicode_ci NOT NULL,
  `added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
	);
    $htaccessFile = '.htaccess';
    if (file_exists('../plugins/DownloadLogger/'.$htaccessFile))
    {
        if (!copy('../plugins/DownloadLogger/'.$htaccessFile.'_files', '../archive/files/'.$htaccessFile))
        {
            throw new Exception("Could not copy '$htaccessFile'", 101);
        }
    }
}

function download_uninstall()
{
    $htaccessFile = '.htaccess';
    if (file_exists('../archive/files/'.$htaccessFile))
        unlink('../archive/files/'.$htaccessFile);
}

function download_config()
{
}

function download_config_form()
{  
}

function download_admin_append_to_items_show()
{
    echo download_display_download();
}


function download_display_download() 
{
    ob_start();
    $item = get_current_item();
    $files = $item->getFiles();
    if (sizeof($files)>0)
    {
        echo "<h2>Downloads</h2>";    
        require_once '../plugins/DownloadLogger/Download.php';
        $dl = new Download();
        $res = $dl->getByItem($item);
        if (sizeof($res) > 0)
        {
            echo "<table><tr><td><b>Downloader</b></td><td><b>Date</b></td></tr>\n";
            foreach ($res as $d_item)
                echo "<tr><td>{$d_item->get_guest_name()} [{$d_item->get_guest_ip()}]</td><td>{$d_item->get_added()}</td></tr>\n";
            echo "</table>";
        }
        else
            echo "<div class='element-text-empty'>[Nobody downloaded]</div>";
    }
    $ht = ob_get_contents();
    ob_end_clean();
    return $ht;
}
