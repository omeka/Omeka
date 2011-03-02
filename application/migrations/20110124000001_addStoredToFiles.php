<?php

class addStoredToFiles extends Omeka_Db_Migration
{
    public function up()
    {
        $db = $this->getDb();

        $db->query("ALTER TABLE `{$db->File}` ADD `stored` TINYINT(1) NOT NULL DEFAULT '0'");
    }

    public function down()
    {
        $db = $this->getDb();

        $db->query("ALTER TABLE `{$db->File}` DROP `stored`");
    }
}
