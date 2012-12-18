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
        
        
/* Item Type Meatadata */        


        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array("Any textual data included in the document", "Text"));
        
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array("The person(s) performing the interview", "Interviewer"));
        
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array("The person(s) being interviewed", "Interviewee"));
        
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array("The location of the interview", "Location"));
        
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array("Any written text transcribed from a sound", "Transcription"));
        
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array("The URL of the local directory containing all assets of the website", "Local URL"));
        
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array("The type of object, such as painting, sculpture, paper, photo, and additional data", "Original Format"));
        
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array("The actual physical size of the original image", "Physical Dimensions"));
        
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array("Length of time involved (seconds, minutes, hours, days, class periods, etc.)", "Duration"));
        
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array("Type/rate of compression for moving image file (i.e. MPEG-4)", "Compression"));
        
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array("Name (or names) of the person who produced the video", "Producer"));
        
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array("Name (or names) of the person who produced the video", "Director"));
        
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array("Rate at which bits are transferred (i.e. 96 kbit/s would be FM quality audio)", "Bit Rate/Frequency"));
        
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array("A summary of an interview given for different time stamps throughout the interview", "Time Summary"));
        
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array("The main body of the email, including all replied and forwarded text and headers", "Email Body"));
        
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array("The content of the subject line of the email", "Subject Line"));
        
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array("The name and email address of the person sending the email", "From"));
        
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array("The name(s) and email address(es) of the person to whom the email was sent", "To"));
        
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array("The name(s) and email address(es) of the person to whom the email was carbon copied", "CC"));
        
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array("The name(s) and email address(es) of the person to whom the email was blind carbon copied", "BCC"));
        
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array("The number of attachments to the email", "Number of Attachments"));
        
        $sql = "UPDATE {$this->db->Element} SET description = ? WHERE name = ?";
        $this->db->query($sql, array("Names of individuals or groups participating in the event", "Participants"));
        
        
        
    }
}
