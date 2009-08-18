<?php
/**
 * Adds the `version` to the plugin table.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class addPluginVersion extends Omeka_Db_Migration
{
    public function up()
    {
        // This adds the version column to the plugins table.
        $db = get_db();
        $sql = "ALTER TABLE `{$db->prefix}plugins` ADD `version` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;";
        $db->query($sql);
        
        // This loops through all the records in the plugins table, and updates the version for each plugin
        $plugins = $db->getTable('Plugin')->findAllWithIniFiles();
        foreach($plugins as $plugin) {
            $pluginVersion = get_plugin_ini($plugin->name, 'version');
            if($pluginVersion) {
                $plugin->version = $pluginVersion;
                $plugin->save();
            }
        }
    }
}
