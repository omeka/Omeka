<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Db\Table
 */
class Table_Element extends Omeka_Db_Table
{   
    /**
     * Find all the Element records that have a specific record type or the
     * record type 'All', indicating that these elements would apply to any
     * record type.
     * 
     * @param string
     * @return array
     */
    public function findByRecordType($recordTypeName)
    {
        $select = $this->getSelect();
        $db = $this->getDb();

        $select->where('element_sets.record_type = ? OR element_sets.record_type IS NULL', $recordTypeName);
        
        $this->orderElements($select);
        
        return $this->fetchObjects($select);
    }
    
    /**
     * Overriding getSelect() to always return the type_name and type_regex
     * for retrieved elements.
     * 
     * @return Omeka_Db_Select
     */
    public function getSelect()
    {
        $select = parent::getSelect();
        $db = $this->getDb();
            
        // Join on the element_sets table to retrieve set name
        $select->joinLeft(array('element_sets'=>$db->ElementSet), 'element_sets.id = elements.element_set_id',
            array('set_name'=>'element_sets.name'));
        return $select;
    }
    
    /**
     * Return the element's name and id for <select> tags on it.
     * 
     * @see Omeka_Db_Table::findPairsForSelectForm()
     * @param string
     * @return void
     */
    protected function _getColumnPairs()
    {
        return array('elements.id', 'elements.name');
    }
        
    protected function orderElements($select)
    {
        // ORDER BY e.order ASC, ISNULL(e.order), es.name ASC
        // This SQL statement will return results ordered each element set,
        // and for each element set these will be in the proper designated order.
        $select->order('elements.element_set_id ASC');
        $select->order('ISNULL(elements.order)');
        $select->order('elements.order ASC');
    }
    
    /**
     * Retrieve all elements for a set.
     * 
     * @see element_set_form()
     * @param string The name of the set to which elements belong.
     * @return Element
     */
    public function findBySet($elementSet)
    {
        // Select all the elements for a given set
        $select = $this->getSelect();
        $db = $this->getDb();
        
        $select->where('element_sets.name = ?', (string) $elementSet);
        
        $this->orderElements($select);
        
        return $this->fetchObjects($select);       
    }
    
    /**
     * Retrieve a set of Element records that belong to a specific Item Type.
     * 
     * @see Item::getItemTypeElements()
     * @param integer
     * @return array Set of element records.
     */
    public function findByItemType($itemTypeId)
    {
        $select = $this->getSelect();
        $db = $this->getDb();
        $select->joinInner(array('item_types_elements'=>$db->ItemTypesElements), 'item_types_elements.element_id = elements.id', array());
        $select->where('item_types_elements.item_type_id = ?');
        $select->order('item_types_elements.order ASC');
        
        $elements = $this->fetchObjects($select, array($itemTypeId)); 

       return $elements;
    }
    
    public function findByElementSetNameAndElementName($elementSetName, $elementName)
    {
        $select = $this->getSelectForFindBy(array('element_set_name' => $elementSetName, 'element_name' => $elementName));
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
     */
    public function applySearchFilters($select, $params)
    {
        $db = $this->getDb();
        
        // Retrieve only elements matching a specific record type.
        if (array_key_exists('record_types', $params)) {
            $where = array();
            foreach ($params['record_types'] as $recordTypeName) {
                if ($recordTypeName == 'All') {
                    $where[] = 'element_sets.record_type IS NULL';
                } else {
                    $where[] = 'element_sets.record_type = ' . $db->quote($recordTypeName);
                }
            }
            $select->where('(' . join(' OR ', $where) . ')');
        }
        
        if (array_key_exists('sort', $params)) {
            if ($params['sort'] == 'alpha') {
                $select->order('elements.name ASC');
            } else if ($params['sort'] == 'alphaBySet') {
                $select->order('element_sets.name ASC')->order('elements.name ASC');
            } else if ($params['sort'] == 'orderBySet') {
                $this->orderElements($select);
            }
        }
        
        if (array_key_exists('element_set_name', $params)) {
            $select->where('element_sets.name = binary ?', (string) $params['element_set_name']);
        }

        if (array_key_exists('element_name', $params)) {
            $select->where('elements.name = binary ?', (string) $params['element_name']); 
        }

        // Retrive results including, but not limited to, a specific item type.
        if (array_key_exists('item_type_id', $params)) {
            $select->joinLeft(array('item_types_elements' => $db->ItemTypesElements),
                'item_types_elements.element_id = elements.id', array());
            $select->where('item_types_elements.item_type_id = ? OR item_types_elements.item_type_id IS NULL', 
                (int)$params['item_type_id']);
        } else if (array_key_exists('exclude_item_type', $params)) {
            $select->where('element_sets.name != ?', ElementSet::ITEM_TYPE_NAME);
        } else if(array_key_exists('item_type', $params)) {
            //for the API for item_types
            $select->joinLeft(array('item_types_elements' => $db->ItemTypesElements),
                    'item_types_elements.element_id = elements.id', array());
            $select->where('item_types_elements.item_type_id = ? ', (int)$params['item_type']);            
        }
        
        // REST API params.
        if (array_key_exists('name', $params)) {
            $select->where("elements.name = ?", $params['name']);
        }
        if (array_key_exists('element_set', $params)) {
            $select->where("elements.element_set_id = ?", $params['element_set']);
        }
    }
    
    /**
     * Override parent class method to retrieve a multidimensional array of 
     * elements, organized by element set, to be used in Zend's FormSelect view 
     * helper.
     * 
     * @param array $options Set of parameters for searching/filtering results.
     * @see Omeka_Db_Table::findPairsForSelectForm()
     * @return array
     */
    public function findPairsForSelectForm(array $options = array())
    {
        $db = $this->getDb();
        // For backwards-compatibility.
        if (!array_key_exists('record_types', $options)) {
            $options['record_types'] = array('Item', 'All');
        }
        $optgroups = get_option('show_element_set_headings');

        $select = $this->getSelectForFindBy($options);
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->from(array(), array(
            'id' => 'elements.id',
            'name' => 'elements.name',
            'set_name' => 'element_sets.name',
        ));

        $elements = $this->fetchAll($select);
        $selectOptions = array();
        foreach ($elements as $element) {
            if ($optgroups) {
                $selectOptions[__($element['set_name'])][$element['id']] = __($element['name']);
            } else {
                $selectOptions[$element['id']] = __($element['name']);
            }
        }

        return $selectOptions;
    }
}
