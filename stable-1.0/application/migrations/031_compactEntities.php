<?php
/**
 * Removes 'type' and 'parent_id' from the entities table, so that there
 * will no longer be nested relationships between people and institutions in this
 * table.  It has caused a lot of confusion and has been compromised by a bug that
 * allowed data to be entered into the 'institution' field that was different than
 * the institution referred to by the 'parent_id' field (see Ticket #526).
 *
 * @package Omeka_Migrations
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class compactEntities extends Omeka_Db_Migration
{
    // Notes on implementation:
    // 
    // Select all entities with empty institution fields that have parent entities.
    // If they do not have empty institution fields or parent entities, we should just leave them alone.
    // SELECT e.*, parent.institution as parent_institution FROM entities e 
    // LEFT JOIN entities parent ON parent.id = e.parent_id 
    // WHERE (e.parent_id IS NOT NULL AND (e.institution IS NULL OR TRIM(e.institution) = ''));
    // 
    // Final table should match the output of this SQL statement:
    // SELECT e.id, e.first_name, e.middle_name, e.last_name, e.email, 
    // IF(parent.institution IS NOT NULL AND (e.institution IS NULL OR TRIM(e.institution) = ''), parent.institution, e.institution)
    // as institution FROM entities e 
    //     LEFT JOIN entities parent ON parent.id = e.parent_id;
    // 
    // Update all entities that have both A) parent entities and B) empty or null
    // 'institution' values.  After this update the 'parent_id' fields will be dropped.
    // UPDATE entities e, entities ep 
    //     SET e.institution = ep.institution
    // WHERE e.parent_id = ep.id AND (e.institution IS NULL OR TRIM(e.institution) = '');
    // 
    // ALTER TABLE entities
    // DROP `type`,
    // DROP `parent_id`;
    
    public function up()
    {
        $prefix = $this->db->prefix;
        $this->db->query(
            "UPDATE {$prefix}entities e, {$prefix}entities ep 
            SET e.institution = ep.institution
            WHERE e.parent_id = ep.id AND (e.institution IS NULL OR TRIM(e.institution) = '');");
        
        $this->db->query(
            "ALTER TABLE {$prefix}entities
            DROP `type`,
            DROP `parent_id`;");
    }
}