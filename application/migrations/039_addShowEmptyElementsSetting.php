<?php
/**
 * Adds the `version` to the plugin table.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class addShowEmptyElementsSetting extends Omeka_Db_Migration
{
    public function up()
    {
        $db = get_db();
        $optionsTable = "`{$db->prefix}options`";

        // Sets the default for the 'show_empty_elements' setting to true for
        // upgrades, unless a setting already exists.
        $existingSql = "SELECT `id` FROM $optionsTable WHERE `name` = 'show_empty_elements'";
        if (!$db->fetchOne($existingSql)) {
            $addSql = "INSERT INTO $optionsTable (`name`, `value`) VALUES ('show_empty_elements', '1');";
            $db->query($addSql);
        }
    }
}
