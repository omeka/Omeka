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
class editElementDescriptions extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {        
        $dcSetDescription = "The Dublin Core metadata element set is common to all Omeka records, including items, files, and collections. For more information see, http://dublincore.org/documents/dces/.";        
        $sql = "UPDATE {$this->db->ElementSet} SET description = ? WHERE name = ?";
        $this->db->query($sql, array($dcSetDescription, 'Dublin Core'));
        
        $dcTitle = "A name given to the resource";
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array($dcTitle, 'Title'));
        
        
        $dcCoverage = "The spatial or temporal topic of the resource, the spatial applicability of the resource, or the jurisdiction under which the resource is relevant";
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array($dcCoverage, 'Coverage'));        
        
        $dcSubject = "The topic of the resource";
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array($dcSubject, 'Subject'));        
        
        
        $dcCreator = "An entity primarily responsible for making the resource";
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array($dcCreator, 'Creator'));        
        
        $dcDescription = "An account of the resource";
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array($dcDescription, 'Description'));        
        
        $dcIdentifier = "An unambiguous reference to the resource within a given context";
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array($dcIdentifier, 'Identifier'));        
        
        
        $dcFormat = "The file format, physical medium, or dimensions of the resource";
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array($dcFormat, 'Format'));        
        
        
        $dcContributor = "An entity responsible for making contributions to the resource";
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array($dcContributor, 'Contributor'));        
        
        
        $dcSource = "A related resource from which the described resource is derived";
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array($dcSource, 'Source'));        
        
        $dcRelation = "A related resource";
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array($dcRelation, 'Relation'));        
        
        $dcRights = "Information about rights held in and over the resource";
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array($dcRights, 'Rights'));        
        
        $dcType = "The nature or genre of the resource";
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array($dcType, 'Type'));        
        
        $dcPublisher = "An entity responsible for making the resource available";
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array($dcPublisher, 'Publisher'));        
        
        $dcLanguage = "A language of the resource";
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array($dcLanguage, 'Language'));        
        
        $dcDate = "A point or period of time associated with an event in the lifecycle of the resource";
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array($dcDate, 'Date'));        
        
        
    }
}
