<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Mixins
 * @author CHNM
 */

/**
 * Record mixin class for associating elements, element texts and their
 * corresponding behaviors to a record.
 *
 * @package Omeka
 * @subpackage Mixins
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class ActsAsElementText extends Omeka_Record_Mixin
{
    
    /**
     * ElementText records stored in the order they were retrieved from the database.
     *
     * @var array
     */
    protected $_textsByNaturalOrder = array();
    
    /**
     * ElementText records indexed by the element_id.
     * 
     * @var array
     */
    protected $_textsByElementId = array();
    
    /**
     * Element records in the order they were retrieved from the database.
     *
     * @var array
     */
    protected $_elementsByNaturalOrder = array();
    
    /**
     * Element records indexed by name and element set name, so that it looks like this:
     * 
     * $elements['Title']['Dublin Core'] = Element instance;
     *
     * @var array
     */
    protected $_elementsByNameAndSet = array();
    
    /**
     * Element records indexed by set name, so that it looks like:
     * 
     * $elements['Dublin Core'] = array(Element instance, Element instance, ...).
     *
     * @var array
     */
    protected $_elementsBySet = array();
    
    /**
     * Element records indexed by ID.
     *
     * @var array
     */
    protected $_elementsById = array();
    
    /**
     * List of elements that were output on the form.  This can be used to 
     * determine the DELETE SQL to use to reset the elements when saving the form.
     *
     * @see ActsAsElementText::_getElementTextsToSaveFromPost()
     * @var array
     */
    protected $_elementsOnForm = array();
    
    /**
     * Set of ElementText records to save when submitting the form.  These will 
     * only be saved to the database if they successfully validate.
     *
     * @var array
     */
    protected $_textsToSave = array();
    
    /**
     * Whether the elements and texts have been loaded yet.
     *
     * @var bool
     */
    protected $_recordsAreLoaded = false;
    
    /**
     * Sets of Element records indexed by record type.
     * 
     * @var array 
     */
    private static $_elementsByRecordType = array();
    
    /**
     * Link to the underlying record
     *
     * @param Omeka_Record
     */
    public function __construct($record)
    {
        $this->_record = $record;
    }
    
    /**
     * Omeka_Record callback for afterSave. Saves the ElementText records once
     * the associated record is saved.
     */
    public function afterSave()
    {
        $this->saveElementTexts();
    }
    
    /**
     * Get the database object from the associated record.
     *
     * @return Omeka_Db
     */
    private function _getDb()
    {
        return $this->_record->getDb();
    }
    
    /**
     * Get the class name of the associated record (Item, File, etc.).
     *
     * @return string Type of record
     */
    private function _getRecordType()
    {
        return get_class($this->_record);
    }
    
    /**
     * Retrieve the record_type_id for this record.
     * 
     * @return integer
     */
    protected function getRecordTypeId()
    {
        return (int) $this->_getDb()->getTable('RecordType')->findIdFromName($this->_getRecordType());
    }
    
    /**
     * Load all the ElementText records for the given record (Item, File, etc.).
     * These will be indexed by [element_id].
     * 
     * Also load all the Element records and index those by their name and set 
     * name.
     * 
     * @param boolean $reload Whether or not reload all the data that was 
     *                        previously loaded.
     * @return void
     */
    public function loadElementsAndTexts($reload=false)
    {
        if ($this->_recordsAreLoaded and !$reload) {
            return;
        }
        
        $elementTextRecords = $this->getElementTextRecords();
        
        $this->_textsByNaturalOrder = $elementTextRecords;
        $this->_textsByElementId = $this->_indexTextsByElementId($elementTextRecords);        
        $this->_loadElements($reload);
        $this->_recordsAreLoaded = true;
    }
    
    private function _loadElements($reload = false)
    {
        $recordType = $this->_getRecordType();
        if (!array_key_exists($recordType, self::$_elementsByRecordType) || $reload) {
            $elements = $this->getElements();
            self::$_elementsByRecordType[$recordType] = $elements;
        } else {
            $elements = self::$_elementsByRecordType[$recordType];
        }
        
        $this->_elementsByNaturalOrder = $elements;
        $this->_elementsByNameAndSet = $this->_indexElementsByNameAndSet($elements);
        $this->_elementsBySet = $this->_indexElementsBySet($elements);
        $this->_elementsById = $this->_indexElementsById($elements);
    }
    
    /**
     * Retrieve ALL of the ElementText records for the given record.
     * 
     * @return array Set of ElementText records for the record.
     */
    public function getElementTextRecords()
    {
        return $this->_record->getTable('ElementText')->findByRecord($this->_record);
    }
    
    /**
     * Retrieve ALL of the Element records for the given record.
     * 
     * @return array All Elements that apply to the record's type.
     */
    public function getElements()
    {        
        return $this->_record->getTable('Element')->findByRecordType($this->_getRecordType());
    }

    /**
     * Retrieve all of the record's ElementTexts for the given Element.
     *
     * @param Element $element
     * @return array Set of ElementText records.
     */
    public function getTextsByElement($element)
    {        
        // Load 'em if we need 'em.
        if (!$this->_recordsAreLoaded) {
            $this->loadElementsAndTexts();
        }

        if (array_key_exists($element->id, $this->_textsByElementId)) {
            return $this->_textsByElementId[$element->id];
        } else {
            return array();
        }
    }
    
    /**
     * Retrieve all of the record's ElementTexts for the given element name and
     * element set name.
     *
     * @param string $elementName Element name
     * @param string $elementSetName Element set name
     * @return array Set of ElementText records.
     */
    public function getElementTextsByElementNameAndSetName($elementName, $elementSetName = null)
    {
        $element = $this->getElementByNameAndSetName($elementName, $elementSetName);
        return $this->getTextsByElement($element);
    }
    
    /**
     * Retrieve the Element with the given ID.
     *
     * @param int $elementId
     * @return Element
     */
    public function getElementById($elementId)
    {
        if (!$this->_recordsAreLoaded) {
            $this->loadElementsAndTexts();
        }
        
        if (!array_key_exists($elementId, $this->_elementsById)) {
            throw new Omeka_Record_Exception(__("Cannot find an element with an ID of '%s'!", $elementId));
        }
        
        return $this->_elementsById[$elementId];
    }
    
    /**
     * Retrieve the Element records for the given ElementSet.
     *
     * @param string Element set name
     * @return array Set of Element records
     */
    public function getElementsBySetName($elementSetName)
    {
        if (!$this->_recordsAreLoaded) {
            $this->loadElementsAndTexts();
        }
        
        $elements = @$this->_elementsBySet[$elementSetName];
        return !empty($elements) ? $elements : array();
    }
    
    /**
     * Retrieve ALL the Element records for the object, organized by ElementSet.
     * For example, $elements['Dublin Core'] = array(Element instance, Element instance, ...)
     * 
     * @return array Set of Element records 
     */
    public function getAllElementsBySet()
    {
        if (!$this->_recordsAreLoaded) {
            $this->loadElementsAndTexts();
        }
        
        return $this->_elementsBySet;
    }
    
    /**
     * Retrieve the Element record corresponding to the given element name and
     * (optional) element set name.
     *
     * @param string $elementName
     * @param string $elementSetName
     * @return Element
     */
    public function getElementByNameAndSetName($elementName, $elementSetName = null)
    {
        if (!$this->_recordsAreLoaded) {
            $this->loadElementsAndTexts();
        }
        
        if (!$elementSetName) {
            $element = @$this->_elementsByNameAndSet[$elementName];
            // We can safely assume that $element is an array, even if empty.
            if (count($element) > 1) {
                // If we have more than one element set with an element of that 
                // name, return the first one.
                debug(__('Element name is ambiguous! There is more than one element set containing an element named "%s"!', $elementName));
                return current($element);
            } else if(empty($element)) {
                throw new Omeka_Record_Exception(__('There is no element named "%s"!',$elementName));
            }
            // Grab the first element of the result array.
            $element = current($element);
        } else {
            $elements = $this->_elementsByNameAndSet[$elementName];
            if (!$elements) {
                throw new Omeka_Record_Exception(__('There is no element named "%s"!',$elementName));
            }
            $element = @$elements[$elementSetName];
            if (!$element) {
                throw new Omeka_Record_Exception(__('There is no element named "%1$s" in the set named "%2$s"!',$elementName, $elementSetName));
            }
        }

        return $element;
    }
    
    /**
     * Index a set of ElementTexts based on element ID.
     *
     * @param array $textRecords Set of ElementText records
     * @return array The provided ElementTexts, indexed by element ID.
     */
    private function _indexTextsByElementId($textRecords)
    {
        $indexed = array();
        foreach ($textRecords as $textRecord) {
            $indexed[$textRecord->element_id][] = $textRecord;
        }
        
        return $indexed;
    }
    
    /**
     * Index a set of Elements based on their name. The result is a doubly
     * associative array, with the first key being element name and the second
     * being element set name.  
     * i.e., $indexed['Creator']['Dublin Core'] = Element instance
     * 
     * @param array $elementRecords Set of Element records
     * @return array The provided Elements, indexed as described
     */
    private function _indexElementsByNameAndSet(array $elementRecords)
    {
        $indexed = array();
        foreach($elementRecords as $record) {
            $indexed[$record->name][$record->set_name] = $record;
        }
        return $indexed;        
    }
    
    /**
     * Index a set of Elements based on their set name.
     *
     * @todo May need to apply ksort() to this to ensure that all sub-arrays are 
     *       in the correct order.
     * @todo May need to optimize this method so we avoid three foreach loops. 
     *       Somehow using SQL to auto-order unordered elements?
     * 
     * @param array
     * @return array
     */
    private function _indexElementsBySet(array $elementRecords)
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
    
    /**
     * Indexes the elements returned by element ID.
     * 
     * @param array
     * @return array
     */
    private function _indexElementsById(array $elementRecords)
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
     * Creates a new ElementText record, populates it with the specified text 
     * value and assigns it to the element.
     * 
     * saveElementTexts() must be called after this in order to save the element
     * texts to the database.  
     * 
     * @param Element $element Element which text should be created for
     * @param string $elementText Text to be added
     * @param bool $isHtml Whether the text to add is HTML
     */
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
     * Add element texts for a record based on a formatted array of values.
     * The array must be formatted as follows:
     * 
     * <code>
     *              'Element Set Name' => 
     *                  array('Element Name' => 
     *                      array(array('text' => 'foo', 'html' => false)))
     * </code>
     *
     * Since 1.4, the array can also be formatted thusly:
     *
     * <code>
     *      array(
     *          array('element_id' => 1,
     *                'text' => 'foo',
     *                'html' => false)
     *      )
     * </code>
     * 
     * @param array $elementTexts
     */
    public function addElementTextsByArray(array $elementTexts)
    {
        if (isset($elementTexts[0]['element_id'])) {
            $this->_addTextsByElementId($elementTexts);
        } else {
            $this->_addTextsByElementName($elementTexts);
        }
    }

    private function _addTextsByElementName(array $elementTexts)
    {
        foreach ($elementTexts as $elementSetName => $elements) {
            foreach ($elements as $elementName => $elementTexts) {
                $element = $this->getElementByNameAndSetName($elementName, $elementSetName);
                foreach ($elementTexts as $elementText) {
                    if (!array_key_exists('text', $elementText)) {
                        throw new Omeka_Record_Exception(__('Element texts are not formatted correctly!'));
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

    private function _addTextsByElementId(array $texts)
    {
        foreach ($texts as $key => $info) {
            if (empty($info['text'])) {
                continue;
            }
            $text = new ElementText;
            $text->record_type_id = $this->getRecordTypeId();
            $text->element_id = $info['element_id'];
            $text->record_id = $this->_record->id;
            $text->text = $info['text'];
            $text->html = $info['html'];
            $this->_textsToSave[] = $text;
        }
    }

    /**
     * The application flow is thus:
     *
     *  1) Build ElementText objects from the POST.
     *  2) Validate the ElementText objects and assign error messages if 
     *     necessary.
     *  3) After the item saves correctly, delete all the ElementText records 
     *     for the Item.
     *  4) Save the new ElementText objects to the database.
     *
     * @see Item::beforeSaveForm()
     * 
     * @param array POST data
     */
    public function beforeSaveElements($post)
    {
        $this->_getElementTextsToSaveFromPost($post);
        $this->_validateElementTexts();        
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
     * @param array POST data
     */
    private function _getElementTextsToSaveFromPost($post)
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
                $filterName = array('Save', $this->_getRecordType(), $element->set_name, $element->name);
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
     * Retrieve a text string for an element from POSTed form data.
     *
     * @param array POST data
     * @param Element
     * @return string
     */
    public function getTextStringFromFormPost($postArray, $element)
    {
        // Attempt to override the defaults with plugin behavior.
        $filterName = array(
            'Flatten', 
            $this->_getRecordType(), 
            $element->set_name, 
            $element->name);

        // If no filters, this should return null.
        $flatText = null;
        $flatText = apply_filters($filterName, $flatText, $postArray, $element);
        
        // If we got something back, short-circuit the built-in processing.
        if ($flatText) {
            return $flatText;
        }
        
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
                // Elements should always have a default data type in the 
                // database, even if plugins override the default behavior.
                throw new Omeka_Record_Exception(__('Cannot process form input for element with data type "%s"!', $elementDataType));
                break;
        }
    }
        
    /**
     * Validate all the elements one by one.  This is potentially a lot slower
     * than batch processing the form, but it gives the added bonus of being 
     * able to encapsulate the logic for validation of Elements.
     */
    private function _validateElementTexts()
    {
        foreach ($this->_textsToSave as $key => $textRecord) {
            if (!$this->_elementTextIsValid($textRecord)) {
                $elementRecord = $this->getElementById($textRecord->element_id);
                $errorMessage = __('The "%s" field has at least one invalid value!', $elementRecord->name);
                $this->_record->addError($elementRecord->name, $errorMessage); 
            }
        }
    }
    
    /**
     * Return whether the given ElementText record is valid.
     *
     * @param ElementText $elementTextRecord
     * @return boolean
     */
    private function _elementTextIsValid($elementTextRecord)
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
                throw new Omeka_Record_Exception(__('Cannot validate an element of data type "%s"!', $elementDataType));
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
        
        $filterName = array('Validate', $this->_getRecordType(), $elementRecord->set_name, $elementRecord->name);
        // Order of the parameters that are passed to this:
        // $isValid = the current value indicating whether or not the element text has validated.
        // $textValue = the string value that needs to be validated
        // $record = the Item or File or whatever record that the element text needs to apply to.
        // $element = the Element record that the text belongs to.
        $isValid = apply_filters($filterName, $isValid, $textValue, $this->_record, $elementRecord);

        return $isValid;
    }
    
    /**
     * Save all ElementText records that were associated with a record.
     *
     * Typically called in the afterSave() hook for a record.
     */
    public function saveElementTexts()
    {        
        if (!$this->_record->exists()) {
            throw new Omeka_Record_Exception(__('Cannot save element text for records that are not yet persistent!'));
        }
        
        // Delete all the elements that were displayed on the form before adding the new stuff.
        $elementIdsFromForm = array_keys($this->_elementsOnForm);
        if (count($elementIdsFromForm)) {
            $this->deleteElementTextsByElementId($elementIdsFromForm);
        }
                
        foreach ($this->_textsToSave as $textRecord) {
            $textRecord->record_id = $this->_record->id;
            $textRecord->forceSave();
        }
        
        // Cause texts to be re-loaded if accessed after save.
        $this->_recordsAreLoaded = false;
    }
    
    /**
     * Delete all the element texts for element_id's that have been provided.
     * 
     * @param array
     * @return boolean
     */
    public function deleteElementTextsByElementId(array $elementIdArray = array())
    {
        $db = $this->_getDb();
        $recordTableName = $this->_record->getTable()->getTableName();
        $recordTypeName = $this->_getRecordType();
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
     * Delete all the element texts assigned to the current record ID.
     *
     * @return boolean
     */
    public function deleteElementTexts()
    {
        $db = $this->_getDb();
        $recordTableName = $this->_record->getTable()->getTableName();
        $recordTypeName = $this->_getRecordType();
        $deleteSql =  "
        DELETE etx FROM $db->ElementText etx 
        INNER JOIN $recordTableName i ON i.id = etx.record_id 
        INNER JOIN $db->RecordType rty ON rty.id = etx.record_type_id
        WHERE rty.name = " . $db->quote($recordTypeName) . " 
        AND i.id = " . $db->quote($this->_record->id) . "";
        return $db->query($deleteSql);
    }
}
