<?php
/**
 * Shorten the Dublin Core element descriptions.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2012
 */
class shortenDcDescriptions extends Omeka_Db_Migration_AbstractMigration
{
    private $_dcElements = array(
        array('name' => 'Contributor', 'description' => 'An entity responsible for making contributions to the resource.'), 
        array('name' => 'Coverage', 'description' => 'The spatial or temporal topic of the resource, the spatial applicability of the resource, or the jurisdiction under which the resource is relevant.'), 
        array('name' => 'Creator', 'description' => 'An entity primarily responsible for making the resource.'), 
        array('name' => 'Date', 'description' => 'A point or period of time associated with an event in the lifecycle of the resource.'), 
        array('name' => 'Description', 'description' => 'An account of the resource.'), 
        array('name' => 'Format', 'description' => 'The file format, physical medium, or dimensions of the resource.'), 
        array('name' => 'Identifier', 'description' => 'An unambiguous reference to the resource within a given context.'), 
        array('name' => 'Language', 'description' => 'A language of the resource.'), 
        array('name' => 'Publisher', 'description' => 'An entity responsible for making the resource available.'), 
        array('name' => 'Relation', 'description' => 'A related resource.'), 
        array('name' => 'Rights', 'description' => 'Information about rights held in and over the resource.'), 
        array('name' => 'Source', 'description' => 'A related resource from which the described resource is derived.'), 
        array('name' => 'Subject', 'description' => 'The topic of the resource.'), 
        array('name' => 'Title', 'description' => 'A name given to the resource.'), 
        array('name' => 'Type', 'description' => 'The nature or genre of the resource.'), 
    );
    
    public function up()
    {
        $sql = <<<SQL
UPDATE {$this->db->Element} e 
JOIN {$this->db->ElementSet} es ON e.element_set_id = es.id 
SET e.description = ? 
WHERE e.name = ?
AND es.name = 'Dublin Core'
SQL;
        foreach ($this->_dcElements as $dcElement) {
            $this->db->query($sql, array($dcElement['description'], $dcElement['name']));
        }
    }
}
