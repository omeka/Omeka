<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Remove Entity dependence from Item and User.
 * 
 * @package Omeka\Db\Migration
 */
class decoupleEntities extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $this->_updateSchema();
        $this->_copyEntityDataToUsers();
        $this->_setItemOwners();
        $this->_addConstraints();
    }

    private function _updateSchema()
    {
        $this->db->queryBlock(<<<SQL
ALTER TABLE `{$this->db->User}`
ADD `name` TEXT COLLATE utf8_unicode_ci AFTER `username`,
ADD `email` TEXT COLLATE utf8_unicode_ci AFTER `name`;

ALTER TABLE `{$this->db->Item}`
ADD `owner_id` INT UNSIGNED DEFAULT NULL,
ADD INDEX `owner_id` (`owner_id`);
SQL
);
    }

    private function _copyEntityDataToUsers()
    {
        $entityData = $this->db->fetchAll(<<<SQL
SELECT u.id, e.first_name, e.middle_name, e.last_name, e.institution, e.email
FROM `{$this->db->User}` AS u
INNER JOIN `{$this->db->Entity}` AS e
ON u.entity_id = e.id
SQL
);

        foreach ($entityData as $row) {
            $nameParts = array();
            if (!empty($row['first_name'])) {
                $nameParts[] = $row['first_name'];
            }
            if (!empty($row['middle_name'])) {
                $nameParts[] = $row['middle_name'];
            }
            if (!empty($row['last_name'])) {
                $nameParts[] = $row['last_name'];
            }
            
            if ($nameParts) {
                $name = implode(' ', $nameParts);
            } else {
                $name = $row['institution'];
            }
            
            $this->db->update($this->db->User,
                array('name' => $name, 'email' => $row['email']),
                array('id = ?' => $row['id'])
            );
        }
    }

    private function _setItemOwners()
    {
        $this->db->query(<<<SQL
UPDATE `{$this->db->Item}` i
INNER JOIN `{$this->db->EntitiesRelations}` er ON er.relation_id = i.id
INNER JOIN `{$this->db->EntityRelationships}` ers ON ers.id = er.relationship_id
INNER JOIN `{$this->db->User}` u ON u.entity_id = er.entity_id
SET i.owner_id = u.id
WHERE ers.name = 'added' AND er.type = 'Item'
SQL
);
    }

    private function _addConstraints()
    {
        $this->db->query(<<<SQL
ALTER TABLE `{$this->db->User}`
MODIFY `name` TEXT COLLATE utf8_unicode_ci NOT NULL,
MODIFY `email` TEXT COLLATE utf8_unicode_ci NOT NULL
SQL
);
    }
}
