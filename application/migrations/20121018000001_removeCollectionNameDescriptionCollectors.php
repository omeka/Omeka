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
class removeCollectionNameDescriptionCollectors extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {        
        // get the names and descriptions from old collections
        $sql = "SELECT id, name, description, collectors FROM `{$this->db->Collection}`";
        $results = $this->db->query($sql)->fetchAll();
        $oldCollections = array();
        foreach($results as $result) {
            $oldCollections[] = array(
                'id' => $result['id'],
                'name' => $result['name'],
                'description' => $result['description'],
                'collectors' => $result['collectors'];
            );
        }
        
        // remove the name and description columns from the Collection table
        $this->db->query("ALTER TABLE `{$this->db->Collection}` DROP `name`");
        $this->db->query("ALTER TABLE `{$this->db->Collection}` DROP `description`");
        $this->db->query("ALTER TABLE `{$this->db->Collection}` DROP `collectors`");
        
        // add the collection names and descriptions as Dublin Core Title and Description element texts 
        $collectionTable = $this->db->getTable('Collection');
        foreach($oldCollections as $oldCollection) {
            $collection = $collectionTable->find(intval($oldCollection['id']));
            if ($collection) {
                $elementTexts = array(
                    'Dublin Core' => array(
                        'Title' => array(array('text' => $oldCollection['name'], 'html' => false)),
                        'Description' => array(array('text' => $oldCollection['description'], 'html' => false)),
                    )
                );
                
                // add collectors as contributors
                $collectorNames = $this->_parseCollectors($oldCollection['collectors']);
                if (count($collectorNames)) {
                    $contributorTexts = array();
                    foreach($collectorNames as $collectorName) {
                         $contributorTexts[] = array('text' => $collectorName, 'html' => false);
                    }
                    $elementTexts['Dublin Core']['Contributor'] = $contributorTexts;
                }
                        
                $collection->addElementTextsByArray($elementTexts);
                $collection->save();
            }
        }
    }
    
    /**
     * Parse a collectors string into an array of collector texts.
     * 
     * @param string $collectors The string of collectors
     * @param string $delimiter the delimiter used to parse the string
     * @return array List of strings.
     * @throws RuntimeException
     */
    protected function _parseCollectors($collectors, $delimiter = "\n")
    {
        if (is_string($collectors)) {
            $collectors = trim($collectors);
            if ($collectors != '') {
                $collectors = explode($delimiter, $collectors);
                $collectors = array_map('trim', $collectors);
                $collectors = array_diff($collectors, array(''));
                $collectors = array_values($collectors);
                return $collectors; 
            }
        }
        return array();
    }
}