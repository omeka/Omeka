<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @package Omeka
 * @subpackage Mixins
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class ActsAsElementText extends Omeka_Record_Mixin
{
    
    /**
     * ElementText records stored in the order they were retrieved from the database.
     *
     * @var array
     **/
    protected $_textsByNaturalOrder = array();
    
    /**
     * ElementText records indexed by the element_id.
     * 
     * @var array
     **/
    protected $_textsByElementId = array();
    
    /**
     * Element records in the order they were retrieved from the database.
     *
     * @var array
     **/
    protected $_elementsByNaturalOrder = array();
    
    /**
     * Element records indexed by name and element set name, so that it looks like this:
     * 
     * $elements['Title']['Dublin Core'] = Element instance;
     *
     * @var array
     **/
    protected $_elementsByNameAndSet = array();
    
    /**
     * Element records indexed by set name, so that it looks like:
     * 
     * $elements['Dublin Core'] = array(Element instance, Element instance, ...).
     *
     * @var array
     **/
    protected $_elementsBySet = array();
    
    /**
     * Element records indexed by ID.
     *
     * @var array
     **/
    protected $_elementsById = array();
    
    /**
     * List of elements that were output on the form.  This can be used to 
     * determine the DELETE SQL to use to reset the elements when saving the form.
     *
     * @see ActsAsElementText::getElementTextsToSaveFromPost()
     * @var array
     **/
    protected $_elementsOnForm = array();
    
    /**
     * Set of ElementText records to save when submitting the form.  These will 
     * only be saved to the database if they successfully validate.
     *
     * @var array
     **/
    protected $_textsToSave = array();
    
    public function __construct($record)
    {
        $this->_record = $record;
    }
    
    public function afterSave()
    {
        $this->saveElementTexts();
    }
    
    public function getDb()
    {
        return $this->_record->getDb();
    }
    
    protected function getRecordType()
    {
        return get_class($this->_record);
    }
    
    /**
     * Retrieve the record_type_id for this record.
     * 
     * @return integer
     **/
    protected function getRecordTypeId()
    {
        return (int) $this->getDb()->getTable('RecordType')->findIdFromName($this->getRecordType());
    }
    
    /**
     * Load all the ElementText records for the given record (Item, File, etc.).  These will be indexed by [element_id].
     * 
     * Also load all the Element records and index those by their name and set name.
     * 
     * @param boolean $reload Whether or not reload all the data that was previously loaded.
     * @return void
     **/
    public function loadElementsAndTexts($reload=false)
    {
        if ($this->_recordsAreLoaded and !$reload) {
            return;
        }
        
        $elementTextRecords = $this->getElementTextRecords();
        
        $this->_textsByNaturalOrder = $elementTextRecords;
        $this->_textsByElementId = $this->indexTextsByElementId($elementTextRecords);
        
        $elements = $this->getElements();
        $this->_elementsByNaturalOrder = $elements;
        $this->_elementsByNameAndSet = $this->indexElementsByNameAndSet($elements);
        $this->_elementsBySet = $this->indexElementsBySet($elements);
        $this->_elementsById = $this->indexElementsById($elements);
        
        $this->_recordsAreLoaded = true;
    }
    
    /**
     * Retrieve ALL of the ElementText records for the given record.
     * 
     * @return array Set of ElementText records for this record.
     **/
    public function getElementTextRecords()
    {
        return $this->_record->getTable('ElementText')->findByRecord($this->_record);
    }
    
    /**
     * Retrieve ALL of the Element records for the given record.
     * 
     * @return array
     **/
    public function getElements()
    {
        return $this->_record->getTable('Element')->findByRecordType($this->getRecordType());
    }

    /**
     * @param string
     * @return array Set of ElementText records.
     **/
    public function getTextsByElement($element)
    {        
        // Load 'em if we need 'em.
        if (!$this->_textsByElementId) {
            $this->loadElementsAndTexts();
        }

        $texts = @$this->_textsByElementId[$element->id];
        return !empty($texts) ? $texts : array();
    }
    
    public function getElementTextsByElementNameAndSetName($elementName, $elementSetName = null)
    {
        $element = $this->getElementByNameAndSetName($elementName, $elementSetName);
        return $this->getTextsByElement($element);
    }
    
    public function getElementById($elementId)
    {
        if (!$this->_elementsById) {
            $this->loadElementsAndTexts();
        }
        
        if (!array_key_exists($elementId, $this->_elementsById)) {
            throw new Exception("Cannot find an element with an ID of '$elementId'!");
        }
        
        return $this->_elementsById[$elementId];
    }
    
    public function getElementsBySetName($elementSetName)
    {
        if (!$this->_elementsBySet) {
            $this->loadElementsAndTexts();
        }
        
        $elements = @$this->_elementsBySet[$elementSetName];
        return !empty($elements) ? $elements : array();
    }
    
    public function getAllElementsBySet()
    {
        if (!$this->_elementsBySet) {
            $this->loadElementsAndTexts();
        }
        
        return $this->_elementsBySet;
    }
    
    public function getElementByNameAndSetName($elementName, $elementSetName = null)
    {
        if (!$this->_elementsByNameAndSet) {
            $this->loadElementsAndTexts();
        }
        
        if (!$elementSetName) {
            $element = @$this->_elementsByNameAndSet[$elementName];
            // We can safely assume that $element is an array, even if empty.
            if (count($element) > 1) {
                // If we have more than one element set with an element of that name,
                // return the first one.
                // throw new Exception('Element name is ambiguous!  There is more than one element set containing an element named "' . $elementName . '"!');
                debug('Element name is ambiguous!  There is more than one element set containing an element named "' . $elementName . '"!');
                return current($element);
            } else if(empty($element)) {
                throw new Exception("There is no element named '$elementName'!");
            }
            // Grab the first element of the result array.
            $element = current($element);
        } else {
            $elements = $this->_elementsByNameAndSet[$elementName];
            if (!$elements) {
                throw new Exception("There is no element named '$elementName'!");
            }
            $element = @$elements[$elementSetName];
            if (!$element) {
                throw new Exception("There is no element named '$elementName' in the set named '$elementSetName'!");
            }
        }

        return $element;
    }
    
    /**
     * @todo Duplicated in ElementTextTable.  Remove from there and put here instead.
     * 
     * @param array
     * @return array
     **/
    public function indexTextsByElementId($textRecords)
    {
        $indexed = array();
        foreach ($textRecords as $textRecord) {
            $indexed[$textRecord->element_id][] = $textRecord;
        }
        
        return $indexed;
    }
    
    /**
     * Index a set of Elements based on their name.
     * 
     * @param array
     * @return array
     **/
    protected function indexElementsByNameAndSet(array $elementRecords)
    {
        $indexed = array();
        foreach($elementRecords as $record) {
            $indexed[$record->name][$record->set_name] = $record;
        }
        return $indexed;        
    }
    
    /**
     * @todo May need to apply ksort() to this to ensure that all sub-arrays are 
     *       in the correct order.
     * @todo May need to optimize this method so we avoid three foreach loops. 
     *       Somehow using SQL to auto-order unordered elements?
     * 
     * @param array
     * @return array
     **/
    protected function indexElementsBySet(array $elementRecords)
    {
        // Account for elements without an order by separating them from 
        // elements with an order.
        $orderedRecords = array();
        $unorderedRecords = array();
        foreach($elementRecords as $record) {
            if ((int) $record->order) {
                $orderedRecords[] = $record;
            } else {
                $unorderedRecords[] = $record;
            }
        }
        // Now build the index by iterating through the ordered elements first 
        // then pushing the unordered elements onto the end of the index in 
        // natural order.
        $indexed = array();
        foreach ($orderedRecords as $orderedRecord) {
            $indexed[$orderedRecord->set_name][(int) $orderedRecord->order] = $orderedRecord;
        }
        foreach ($unorderedRecords as $unorderedRecord) {
            $indexed[$unorderedRecord->set_name][] = $unorderedRecord;
        }
        return $indexed;
    }
    
    protected function indexElementsById(array $elementRecords)
    {
        $indexed = array();
        foreach($elementRecords as $record) {
            $indexed[$record->id] = $record;
        }
        return $indexed;
    }
    
    /**
     * Add a string of text for an element.
     * 
     * Creates a new ElementText record, populates it with the specified text value 
     * and assigns it to the element.
     * 
     * saveElementTexts() must be called after this in order to save the element
     * texts to the database.  
     * 
     * @param string
     * @return void
     **/
    public function addTextForElement($element, $elementText, $isHtml = false)
    {
        $textRecord = new ElementText;
        $textRecord->record_id = $this->_record->id;
        $textRecord->element_id = $element->id;
        $textRecord->record_type_id = $this->getRecordTypeId();
        $textRecord->text = $elementText;
        $textRecord->html = (int)$isHtml;
            
        $this->_textsToSave[] = $textRecord;
    }
    
    /**
     * Adds element texts for a record based on a formatted array of values.
     * The array must be formatted as follows:
     * 
     *              'Element Set Name' => 
     *                  array('Element Name' => 
     *                      array(array('text' => 'foo', 'html' => false)))
     * 
     * @param array $elementTexts
     * @return void
     **/
    public function addElementTextsByArray(array $elementTexts)
    {
        foreach ($elementTexts as $elementSetName => $elements) {
            foreach ($elements as $elementName => $elementTexts) {
                $element = $this->getElementByNameAndSetName($elementName, $elementSetName);
                foreach ($elementTexts as $elementText) {
                    if (!array_key_exists('text', $elementText)) {
                        throw new Exception('Element texts are not formatted correctly!');
                    }
                    // Only add the element text if it's not empty.  There
                    // should be no empty element texts in the DB.
                    if (!empty($elementText['text'])) {
                        $this->addTextForElement($element, $elementText['text'], $elementText['html']);
                    }
                }
            }
        }
    }
    
    /**
     * The application flow is thus:
     *
     *  1) Build ElementText objects from the POST.
     *  2) Validate the ElementText objects and assign error messages if necessary.
     *  3) After the item saves correctly, delete all the ElementText records for the Item.
     *  4) Save the new ElementText objects to the database.
     *
     * @see Item::beforeSaveForm()
     * 
     * @param array
     * @return void
     **/
    public function beforeSaveElements(&$post)
    {
        $this->getElementTextsToSaveFromPost($post);
        $this->validateElementTexts();        
    }

    /**
     * The POST should have a key called "Elements" that contains an array
     * that is keyed to an element's ID.  That array should contain all the 
     * text values for that element. For example:
     *
     *      * Elements:
     *          * 1:
     *              * 0: 
     *                  'text': 'Foobar'
     *                  'html': '0'
     *              * 1: 'Baz'
     * 
     * @todo May want to throw an Exception if an element in the POST doesn't
     * actually exist.
     * @param array
     * @return void
     **/
    public function getElementTextsToSaveFromPost($post)
    {
        
        if (!$elementPost = $post['Elements']) {
            return;
        }
        
        foreach ($elementPost as $elementId => $texts) {
            // Pull this from the list of prior retrieved data instead of a new SQL query each time.
            $element = $this->getElementById($elementId);
            
            // Add this to the stack of elements that are stored on the form.
            $this->_elementsOnForm[$element->id] = $element;
            
            foreach ($texts as $key => $textAttributes) {
                $elementText = $this->getTextStringFromFormPost($textAttributes, $element);
                
                // Save element text filter.
                $filterName = array('Save', $this->getRecordType(), $element->set_name, $element->name);
                $elementText = apply_filters($filterName, $elementText, $this->_record, $element);
                
                // Ignore fields that are empty (no text)
                if (empty($elementText)) {
                    continue;
                }
                
                $isHtml = (int) (boolean) $textAttributes['html'];
                $this->addTextForElement($element, $elementText, $isHtml);
            }
        }
    }
    
    /**
     * @todo Hook into plugins.
     * @param array
     * @param Element
     * @return string
     **/
    public static function getTextStringFromFormPost($postArray, $element)
    {
        $elementDataType = $element->data_type_name;
        switch ($elementDataType) {
            case 'Tiny Text':
            case 'Text':
            case 'Integer':
                return $postArray['text'];
                break;
            case 'Date':
                // Almost forgot that I had already created this awhile back.
                $dateFilter = new Omeka_Filter_Date;
                return $dateFilter->filter($postArray['year'], 
                                        $postArray['month'], 
                                        $postArray['day']);
            case 'Date Range':
                $dateFilter = new Omeka_Filter_Date;
                $startDate = $dateFilter->filter($postArray['start']['year'], 
                                        $postArray['start']['month'], 
                                        $postArray['start']['day']);
                $endDate = $dateFilter->filter($postArray['end']['year'], 
                                        $postArray['end']['month'], 
                                        $postArray['end']['day']);
                // Should come out to be start date and end date separated by a space.
                // Or if we don't have either a start or end date, it should not store anything.
                if (!$startDate && !$endDate) {
                    return null;
                }
                return $startDate . ' ' . $endDate;
            case 'Date Time':
                $dateFilter = new Omeka_Filter_Date;
                $date = $dateFilter->filter($postArray['year'], 
                                            $postArray['month'], 
                                            $postArray['day']);
                $timeFilter = new Omeka_Filter_Time;
                $time = $timeFilter->filter($postArray['hour'], 
                                            $postArray['minute'], 
                                            $postArray['second']);
                if (!$date && !$time) {
                    return null;
                }
                return "$date $time";
            default:
                throw new Exception("Cannot process form input for element with data type '$elementDataType'!");
                break;
        }
    }
        
    /**
     * Validate all the elements one by one.  This is potentially a lot slower
     * than batch processing the form, but it gives the added bonus of being 
     * able to encapsulate the logic for validation of Elements.
     * 
     * @param array Set of Element records.
     * @return void
     **/
    public function validateElementTexts()
    {
        foreach ($this->_textsToSave as $key => $textRecord) {
            if (!$this->elementTextIsValid($textRecord)) {
                $elementRecord = $this->getElementById($textRecord->element_id);
                $errorMessage = "'$elementRecord->name' field has at least one invalid value!";
                $this->_record->addError($elementRecord->name, $errorMessage); 
            }
        }
    }
    
    /**
     * @todo Testing.
     * @todo Plugins must hook into this.
     * @param string
     * @return void
     **/
    public function elementTextIsValid($elementTextRecord)
    {
        $elementRecord = $this->getElementById($elementTextRecord->element_id);
        $textValue = $elementTextRecord->text;
        $elementDataType = $elementRecord->data_type_name;
        // Start out as valid by default.
        $isValid = true;
        $validators = array(
            'Tiny Text' => null,
            'Text'      => null,
            'Integer'   => 'Zend_Validate_Int',
            'Date'      => 'Omeka_Validate_PartialDate',
            'Date Range'=> 'Omeka_Validate_PartialDateRange',
            'Date Time' => 'Omeka_Validate_DateTime');
        
        // Empty values validate by default b/c it just means they won't
        // be saved to the database.    
        if (!empty($textValue)) {
            // Even for plugins hooking into the validation, each element must
            // have one of these default data types.
            if (!array_key_exists($elementDataType, $validators)) {
                throw new Exception("Cannot validate an element of data type '$elementDataType'!");
            }
            $validatorClass = $validators[$elementDataType];
            // Text and Tiny Text have no default validation so skip those.
            if ($validatorClass) {
                $validator = new $validatorClass;
                $isValid = $validator->isValid($textValue);
            }
        }

        // Hook into this for plugins.
        // array('Validate', 'Item', 'Title', 'Dublin Core')
        // add_filter(array('Validate', 'Item', 'Title', 'Dublin Core'), 'my_filter_name');
        
        // function my_filter_name($isValid, $elementText, $item, $element)
        // {
        //      if (!in_array($elementText, array('foo'))) {
        //          return false;
        //      }
        // }
        
        $filterName = array('Validate', $this->getRecordType(), $elementRecord->set_name, $elementRecord->name);
        // Order of the parameters that are passed to this:
        // $isValid = the current value indicating whether or not the element text has validated.
        // $textValue = the string value that needs to be validated
        // $record = the Item or File or whatever record that the element text needs to apply to.
        // $element = the Element record that the text belongs to.
        $isValid = apply_filters($filterName, $isValid, $textValue, $this->_record, $element);

        return $isValid;
    }
    
    /**
     * Save all ElementText records that were associated with a record.
     *
     * Typically called in the afterSave() hook for a record.
     * 
     * @return void
     **/
    public function saveElementTexts()
    {        
        if (!$this->_record->exists()) {
            throw new Exception('Cannot save element text for records that are not yet persistent!');
        }
        
        // var_dump($this->_textsToSave);exit;
        
        // Delete all the elements that were displayed on the form before adding the new stuff.
        $elementIdsFromForm = array_keys($this->_elementsOnForm);
        if (count($elementIdsFromForm)) {
            $this->deleteElementTextsByElementId($elementIdsFromForm);
        }
                
        foreach ($this->_textsToSave as $textRecord) {
            $textRecord->record_id = $this->_record->id;
            $textRecord->forceSave();
        }
    }
    
    /**
     * Deletes all the element texts for element_id's that have been provided.
     * 
     * @param array
     * @return boolean
     **/
    public function deleteElementTextsByElementId(array $elementIdArray = array())
    {
        $db = $this->getDb();
        $recordTableName = $this->_record->getTable()->getTableName();
        $recordTypeName = $this->getRecordType();
        // For some reason, this needs the parameters to be quoted directly into the
        // SQL statement in order for the DELETE to work. It may have something to
        // do with quoting the array of element IDs into a string.
        $deleteSql =  "
        DELETE etx FROM $db->ElementText etx 
        INNER JOIN $recordTableName i ON i.id = etx.record_id 
        INNER JOIN $db->RecordType rty ON rty.id = etx.record_type_id
        WHERE rty.name = " . $db->quote($recordTypeName) . " 
        AND i.id = " . $db->quote($this->_record->id) . " 
        AND etx.element_id IN (" . $db->quote($elementIdArray) . ")";
        return $db->query($deleteSql);
    }
    
    /**
     * Deletes all the element texts assigned to the current record ID.
     * @return boolean
     **/
    public function deleteElementTexts()
    {
        $db = $this->getDb();
        $recordTableName = $this->_record->getTable()->getTableName();
        $recordTypeName = $this->getRecordType();
        $deleteSql =  "
        DELETE etx FROM $db->ElementText etx 
        INNER JOIN $recordTableName i ON i.id = etx.record_id 
        INNER JOIN $db->RecordType rty ON rty.id = etx.record_type_id
        WHERE rty.name = " . $db->quote($recordTypeName) . " 
        AND i.id = " . $db->quote($this->_record->id) . "";
        return $db->query($deleteSql);
    }
}
