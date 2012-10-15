<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Add the table containing search text.
 * 
 * @package Omeka\Db\Migration
 */
class addSearchText extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `{$this->db->SearchText}` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `record_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `record_id` int(10) unsigned NOT NULL,
  `public` BOOLEAN NOT NULL,
  `title` TINYTEXT NULL DEFAULT NULL,
  `text` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `record_name` (`record_name`,`record_id`),
  FULLTEXT KEY `text` (`text`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SQL;
        $this->db->query($sql);
    }
}
