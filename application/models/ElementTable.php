<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/
 
/**
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class ElementTable extends Omeka_Db_Table
{
    /**
     * Find all of the element records that apply to Items.
     * 
     * @todo There should be a lightweight method that retrieves only the
     *  elements that are actually used for this item. This method itself should
     *  be used primarily for retrieving data for the forms, since that is the
     *  most likely instance where all of the Element records will be needed.
     * @return array
     **/
    public function findAllForItems()
    {
        $select = $this->getSelect();
        $db = $this->getDb();

        // Join against the record_types table to pull only elements for Items
         $select->joinInner(array('rty'=> $db->RecordType),
                             'rty.id = e.record_type_id',
                             array('record_type_name'=>'rty.name'));
        $select->where('rty.name = "Item" OR rty.name = "All"');

        $objs = $this->fetchObjects($select);
        return $this->indexByNameAndSet($objs);
    }
    
    /**
     * Find all the Element records that have a specific record type or the
     * record type 'All', indicating that these elements would apply to any
     * record type.
     * 
     * @param string
     * @return array
     **/
    public function findByRecordType($recordTypeName)
    {
        $select = $this->getSelect();
        $db = $this->getDb();

        // Join against the record_types table to pull only elements for Items
         $select->joinInner(array('rty'=> $db->RecordType),
                             'rty.id = e.record_type_id',
                             array('record_type_name'=>'rty.name'));
        $select->where('rty.name = ? OR rty.name = "All"', $recordTypeName);

        return $this->fetchObjects($select);
    }
    
    public function findForFilesByMimeType($mimeType = null)
    {
        $db = $this->getDb(); 
        $sqlMimeTypeElements = $this->getSelect()
        ->joinInner(array('mesl'=>$db->MimeElementSetLookup), 'mesl.element_set_id = es.id')
        ->where('mesl.mime = ?', $mimeType);
        
        return $this->fetchObjects($sqlMimeTypeElements);        
    }
    
    /**
     * Overriding getSelect() to always return the type_name and type_regex
     * for retrieved elements.
     * 
     * @return Omeka_Db_Select
     **/
    public function getSelect()
    {
        $select = parent::getSelect();
        $db = $this->getDb();
        // Join on the element_types table to retrieve type regex and type name
        $select->joinLeft(array('dt'=>$db->DataType), 'dt.id = e.data_type_id', 
            array('data_type_name'=>'dt.name'));
            
        // Join on the element_sets table to retrieve set name
        $select->joinLeft(array('es'=>$db->ElementSet), 'es.id = e.element_set_id',
            array('set_name'=>'es.name'));
        return $select;
    }
    
    /**
     * Index a set of Elements based on their name.
     * 
     * @todo Deprecate and move to ActsAsElementText::indexByNameAndSet().
     * @param array
     * @return array
     **/
    protected function indexByNameAndSet(array $elementRecords)
    {
        $indexed = array();
        foreach($elementRecords as $record) {
            $indexed[$record->name][$record->set_name] = $record;
        }
        return $indexed;        
    }

    /**
     * Return the element's name and id for <select> tags on it.
     * 
     * @see Omeka_Db_Table::findPairsForSelectForm()
     * @param string
     * @return void
     **/
    protected function _getColumnPairs()
    {
        return array('e.id', 'e.name');
    }
    
    /**
     * Overridden to natsort() the columns.
     * 
     * @return array
     **/
    public function findPairsForSelectForm()
    {
        $pairs = parent::findPairsForSelectForm();
        natsort($pairs);
        return $pairs;
    }
    
    /**
     * Retrieve all elements for a set (containing text only for the item)
     * 
     * @see items/form.php
     * @see display_form_input_for_element()
     * @param Item
     * @param string The name of the set it belongs to.
     * @return Element
     **/
    public function findForItemBySet($item, $elementSet)
    {
        // Select all the elements for a given set
        $select = $this->getSelect();
        $db = $this->getDb();
        
        $select->where('es.name = ?', (string) $elementSet);
        
        $elements = $this->fetchObjects($select);
       
       // Populate those element records with the values for a given item.
       // Do this because display_form_input_for_element() requires the text records.
       // Cannot use the cached values ($item->ElementTexts) because they may have been filtered already.
       return $this->assignTextToElements($elements, $item->getElementText());
    }
    
    /**
     * Assign a set of Element texts to a set of Elements.
     *
     * @internal I'm not sure this belongs in the ElementTable class, because its
     * not a finder method, but currently the code is split across multiple places
     * and this is an attempt to consolidate it.
     * @param array Set of Element records.
     * @param array Set of ElementText records.
     * @return array Set of elements with text assigned to it.
     **/
    public function assignTextToElements($elements, $textRecords)
    {
        foreach ($elements as $key => $element) {
            // ElementText records are indexed by element_id
            if ($textRecordSet = @$textRecords[$element->id]) {
                // This could be shortened such to cut out the extra foreach loop.
                foreach ($textRecordSet as $record) {
                    $element->addText($record);
                }
            }
        }
        
        return $elements;
    }
    
    /**
     * Retrieve a set of Element records that belong to a specific Item Type.
     * 
     * @see Item::getItemTypeElements()
     * @param integer
     * @return array Set of element records.
     **/
    public function findByItemType($itemTypeId)
    {
        $select = $this->getSelect();
        $db = $this->getDb();
        $select->joinInner(array('ite'=>$db->ItemTypesElements), 'ite.element_id = e.id', array());
        $select->where('ite.item_type_id = ?');
        
        $elements = $this->fetchObjects($select, array($itemTypeId)); 

       return $elements;
    }
}
