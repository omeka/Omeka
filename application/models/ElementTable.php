<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/
 
/**
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class ElementTable extends Omeka_Db_Table
{   
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
        
        $this->orderElements($select);
        
        return $this->fetchObjects($select);
    }
    
    public function findForFilesByMimeType($mimeType = null)
    {
        $db = $this->getDb(); 
        $sqlMimeTypeElements = $this->getSelect()
        ->joinInner(array('mesl'=>$db->MimeElementSetLookup), 'mesl.element_set_id = es.id', array())
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
        
    protected function orderElements($select)
    {
        // ORDER BY e.order ASC, es.name ASC
        // This SQL statement will return results ordered each element set,
        // and for each element set these will be in the proper designated order.
        $select->order($this->getTableAlias() . '.order ASC');
        $select->order('e.element_set_id ASC');
    }
    
    /**
     * Retrieve all elements for a set.
     * 
     * @see display_element_set_form()
     * @param string The name of the set to which elements belong.
     * @return Element
     **/
    public function findBySet($elementSet)
    {
        // Select all the elements for a given set
        $select = $this->getSelect();
        $db = $this->getDb();
        
        $select->where('es.name = ?', (string) $elementSet);
        
        $this->orderElements($select);
        
        return $this->fetchObjects($select);       
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
        $select->order('ite.order ASC');
        
        $elements = $this->fetchObjects($select, array($itemTypeId)); 

       return $elements;
    }
    
    public function findByElementSetNameAndElementName($elementSetName, $elementName)
    {
        $select = $this->getSelect()
                       ->where('es.name = ?', $elementSetName)
                       ->where('e.name = ?', $elementName);
        return $this->fetchObject($select);
    }
    
    /**
     * Manipulate a Select object based on a set of criteria.
     * 
     * @param Omeka_Db_Select $select
     * @param array $params Possible parameters include:
     * <ul>
     *      <li>record_types - array - Usually one or more of the following:
     * All, Item, File</li>
     *      <li>sort - string - One of the following values: alpha</li>
     *      <li>element_set_name - string - Name of the element set to which
     * results should belong.</li>
     * </ul>
     **/
    public function applySearchFilters($select, $params)
    {
        $db = $this->getDb();
        
        // Retrieve only elements matching a specific record type.
        if (array_key_exists('record_types', $params)) {
            $select->joinInner(array('rty'=> $db->RecordType),
                                 'rty.id = e.record_type_id',
                                 array('record_type_name'=>'rty.name'));
            foreach ($params['record_types'] as $recordTypeName) {
                $select->orWhere('rty.name = ?', $recordTypeName);
            }
        }
        
        if (array_key_exists('sort', $params)) {
            if ($params['sort'] == 'alpha') {
                $select->order('e.name ASC');
            }
        }
        
        if (array_key_exists('element_set_name', $params)) {
            $select->where('es.name = ?', (string) $params['element_set_name']);
        }
    }
}
