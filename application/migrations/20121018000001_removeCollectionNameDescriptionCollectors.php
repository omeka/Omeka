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
        $sql = "SELECT `id`, `name`, `description`, `collectors` FROM `{$this->db->Collection}`";
        $results = $this->db->query($sql)->fetchAll();
        $collections = array();
        foreach($results as $result) {
            $collections[] = array(
                'id' => $result['id'],
                'name' => $result['name'],
                'description' => $result['description'],
                'collectors' => $result['collectors']
            );
        }
        
        // remove the name and description columns from the Collection table
        $this->db->query("ALTER TABLE `{$this->db->Collection}` DROP `name`");
        $this->db->query("ALTER TABLE `{$this->db->Collection}` DROP `description`");
        $this->db->query("ALTER TABLE `{$this->db->Collection}` DROP `collectors`");
        
        // add the collection names and descriptions as Dublin Core Title and Description element texts 
        foreach($collections as $collection) {
            $this->_addTitleElement($collection);
            $this->_addDescriptionElement($collection);
            $this->_addContributors($collection);
        }
    }
    
    /**
     * Adds element text to the database
     * 
     * @param int $recordId The record id for the element text
     * @param string $recordType The record type for the element text
     * @param int $elementId The element id for the element text
     * @param boolean $html Whether the element text is html 
     * @param string $text The text of the element text
     */        
    protected function _addElementText($recordId, $recordType, $elementId, $html, $text)
    {
        $this->db->query("INSERT INTO `{$this->db->ElementText}` (`record_id`, `record_type`, `element_id`, `html`, `text`) VALUES (?, ?, ?, ?, ?)", array(
            $recordId,
            $recordType,
            $elementId,
            $html,
            $text
        ));
    }
    
    /**
     * Returns the element id for a given element set name and element name
     * 
     * @param array $collection The collection
     * @return int
     */
    protected function _getElementId($elementSetName, $elementName)
    {
        $result = $this->db->query("SELECT `a`.`id` FROM `{$this->db->Element}` AS `a`, `{$this->db->ElementSet}` AS `b` WHERE `a`.`element_set_id` = `b`.`id` AND `b`.`name` = ? AND `a`.`name` = ? LIMIT 1", array($elementSetName, $elementName))->fetch();
        return intval($result['id'], 10);
    }

    /**
     * Adds collection title as collection title element text.
     * 
     * @param array $collection The collection
     */
    protected function _addTitleElement($collection)
    {
        $titleElementId = $this->_getElementId('Dublin Core', 'Title'); 
        $this->_addElementText($collection['id'], 'Collection', $titleElementId, false, $collection['name']);
    }
    
    /**
     * Adds collection description as collection description element text.
     * 
     * @param array $collection The collection
     */
    protected function _addDescriptionElement($collection)
    {
        $descriptionElementId = $this->_getElementId('Dublin Core', 'Description');
        $this->_addElementText($collection['id'], 'Collection', $descriptionElementId, false, $collection['description']);
    }
    
    /**
     * Adds collection collectors as collection contributor element texts.
     * 
     * @param array $collection The collection
     */
    protected function _addContributors($collection) 
    {
        // add collectors as contributors
        $collectorNames = $this->_parseCollectors($collection['collectors']);
        if (count($collectorNames)) {            
            $contributorElementId = $this->_getElementId('Dublin Core', 'Contributor');
            foreach($collectorNames as $collectorName) {
                $this->_addElementText($collection['id'], 'Collection', $contributorElementId, false, $collectorName);
            }
        }
    }
    
    /**
     * Parse a collectors string into an array of collector texts.
     * 
     * @param string $collectors The string of collectors
     * @param string $delimiter the delimiter used to parse the string
     * @return array List of strings.
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