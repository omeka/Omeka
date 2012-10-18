<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Db\Migration
 */
class removeCollectionNameAndDescription extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {        
        // get the names and descriptions from old collections
        $sql = "SELECT id, name, description FROM `{$this->db->Collection}`";
        $results = $this->db->query($sql)->fetchAll();
        $oldCollections = array();
        foreach($results as $result) {
            $oldCollections[] = array(
                'id' => $result['id'],
                'name' => $result['name'],
                'description' => $result['description']
            );
        }
        
        // remove the name and description columns from the Collection table
        $this->db->query("ALTER TABLE `{$this->db->Collection}` DROP `name`");
        $this->db->query("ALTER TABLE `{$this->db->Collection}` DROP `description`");
        
        // add the collection names and descriptions as Dublin Core Title and Description element texts 
        $collectionTable = $this->db->getTable('Collection');
        foreach($oldCollections as $oldCollection) {
            $collection = $collectionTable->find(intval($oldCollection['id']));
            if ($collection) {
                $elementTexts = array(
                    'Dublin Core' => array(
                        'Title' => array(array('text' => $oldCollection['name'], 'html' => false)),
                        'Description' => array(array('text' => $oldCollection['description'], 'html' => false))
                    )
                );        
                $collection->addElementTextsByArray($elementTexts);
                $collection->save();
            }
        }
    }
}