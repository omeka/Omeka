<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Models
 */

/**
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2010
 */
class Collection extends Omeka_Record
{        
    const COLLECTION_NAME_MIN_CHARACTERS = 1;
    const COLLECTION_NAME_MAX_CHARACTERS = 255;
    
    const COLLECTOR_DELIMITER = "\n";
    
    /**
     * @var string Name of the collection.
     */
    public $name;
    
    /**
     * @var string Description for the collection.
     */
    public $description = '';
    
    /**
     * @var array Strings containing the names of this collection's collectors.
     */
    public $collectors = array();
    
    /**
     * @var boolean Whether or not the collection is publicly accessible.
     */
    public $public = 0;
    
    /**
     * @var boolean Whether or not the collection is featured.
     */
    public $featured = 0;
    
    /**
     * @var string
     */
    public $added;
    
    /**
     * @var string
     */
    public $modified;
    
    /**
     * @var integer
     */
    public $owner_id = 0;
    
    protected $_related = array('Collectors' => 'getCollectors');
        
    protected function _initializeMixins()
    {
        $this->_mixins[] = new PublicFeatured($this);
    }
    
    /**
     * Determine whether or not the collection has collectors associated with it.
     * 
     * @return boolean
     */
    public function hasCollectors()
    {
        return (boolean)$this->getCollectors();
    }
    
    /**
     * Determine the total number of items associated with this collection.
     * 
     * @return integer
     */
    public function totalItems()
    {
        return $this->getDb()->getTable('Item')->count(array('collection' => $this->id));
    }
    
    /**
     * Retrieve a list of all the collectors associated with this collection.
     * 
     * @return array List of Entity records.
     */
    public function getCollectors()
    {
        if (is_string($this->collectors)) {
            return explode(self::COLLECTOR_DELIMITER, $this->collectors);
        } else if (is_array($this->collectors)) {
            return $this->collectors;
        }
    }

    /**
     * Filter the POST data from the form.
     *
     * Trims the 'name' and 'description' strings, strips tags from the 
     * collection name, and converts public/featured flags to booleans.
     * 
     * @param array $post
     * @return array
     */
    protected function filterInput($post)
    {
        $options = array('inputNamespace'=>'Omeka_Filter');
        
        // User form input does not allow HTML tags or superfluous whitespace
        $filters = array('name'         => array('StripTags','StringTrim'),
                         'description'  => array('StringTrim'),
                         'public'       => 'Boolean',
                         'featured'     => 'Boolean');
            
        $filter = new Zend_Filter_Input($filters, null, $post, $options);
        
        $post = $filter->getUnescaped();
                
        return $post;
    }
    
    /**
     * Validate the record.
     * 
     * Checks the collection name to ensure that it is below 255 characters.
     */
    protected function _validate()
    {        
        if (!Zend_Validate::is($this->name, 'StringLength', array(
                'min' => self::COLLECTION_NAME_MIN_CHARACTERS,
                'max' => self::COLLECTION_NAME_MAX_CHARACTERS))
        ) {
            $this->addError('name', 'The collection name must have between ' 
             . self::COLLECTION_NAME_MIN_CHARACTERS .  ' and ' 
             . self::COLLECTION_NAME_MAX_CHARACTERS .  ' characters.');
        }
    }
    
    /**
     * Remove the association between a given collector Entity and the 
     * collection.
     * 
     * @param string
     * @return boolean Was successful or not.
     **/
    public function removeCollector($collector)
    {
        $collectors = $this->getCollectors();
        if ($foundKey = array_search($collector, $collectors)) {
            unset($collectors[$foundKey]);
            $this->setCollectors($collectors);
            return true;
        }
        return false;
    }
    
    protected function beforeSaveForm($post)
    {
        // Process the collectors that have been provided on the form
        $collectorsPost = $post['collectors'];
        
        $collectors = $this->getCollectors();
        foreach ($collectorsPost as $k => $collectorName) {
            if (!empty($collectorName)) {
                $collectors[] = $collectorName;
            }
        }
        $this->setCollectors($collectors);
    }
    
    /**
     * Add a collector Entity to the collection.
     * 
     * @param string|Entity $collector
     * 
     * @return void
     */
    public function addCollector($collector)
    {
        if (is_string($collector)) {
            $collectorName = $collector;
        } else if ($collector instanceof Entity) {
            $collectorName = $collector->getName();
        } else {
            throw new InvalidArgumentException("Argument passed to addCollector() must be a string or an instance of Entity.");
        }
        $collectors = $this->getCollectors();
        $collectors[] = $collectorName;
        $this->setCollectors($collectors);
    }
    
    public function setCollectors(array $collectorList)
    {
        $this->collectors = $collectorList;
    }
    
    protected function beforeSave()
    {
        $this->_serializeCollectors();
    }
    
    private function _serializeCollectors()
    {
        if (is_array($this->collectors)) {
            $this->collectors = join(self::COLLECTOR_DELIMITER, $this->collectors);
        }
    }            
}