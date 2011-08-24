<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Models
 */

/**
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
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
    public $description;
    
    /**
     * @var array Strings containing the names of this collection's collectors.
     */
    public $collectors;
    
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
     * @return array List of strings.
     */
    public function getCollectors()
    {
        if (!$this->collectors) {
            return array();
        } else if (is_string($this->collectors)) {
            if (trim($this->collectors) == '') {
                return array();
            } else {
                $collectors = explode(self::COLLECTOR_DELIMITER, $this->collectors);
                $collectors = array_map('trim', $collectors);
                $collectors = array_diff($collectors, array(''));
                $collectors = array_values($collectors);
                return $collectors;
            }
        } else if (!is_array($this->collectors)) {
            throw new RuntimeException(__("Collectors must be either a string or an array."));
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
            $this->addError('name', __('The collection name must have between %1$s and %2$s characters.', self::COLLECTION_NAME_MIN_CHARACTERS, 
            self::COLLECTION_NAME_MAX_CHARACTERS));
        }
    }
    
    /**
     * Disassociate a collector with this collection.
     * 
     * @param string
     * @return boolean Was successful or not.
     */
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
        if (isset($post['collectors'])) {
            $collectorPost = (string)$post['collectors'];
            $collectors = explode(self::COLLECTOR_DELIMITER, $collectorPost);
            $this->setCollectors($collectors);
        }        
    }
    
    /**
     * Add a collector to the collection.
     *
     * Note that prior versions of Omeka allowed for entering collector metadata
     * as Entity records.  This behavior has been deprecated and removed in 
     * Omeka >= 1.3.  Please use the new syntax, which is simply the string name
     * for the collector.
     * 
     * @param string|Entity $collector
     * @return void
     */
    public function addCollector($collector)
    {
        if (is_string($collector)) {
            $collectorName = $collector;
        } else if ($collector instanceof Entity) {
            $collectorName = $collector->getName();
        } else {
            throw new InvalidArgumentException(__("Argument passed to addCollector() must be a string or an instance of Entity."));
        }
        $collectorName = trim($collectorName);
        if ($collectorName != '') {
            $this->collectors .= ($this->collectors ? self::COLLECTOR_DELIMITER 
                : ''). $collectorName;
        }
    }
    
    /**
     * Set the list of collectors for this collection.
     * 
     * @param array List of string names of collectors.
     */
    public function setCollectors(array $collectorList)
    {
        $this->collectors = '';
        foreach ($collectorList as $key => $collector) {
            $this->addCollector($collector);
        }
    } 
    
    /**
     * Set the user who added the collection.
     * 
     * Note that this is not to be confused with the collection's "collectors".
     * 
     * @param User $user
     */
    public function setAddedBy(User $user)
    {
        if (!$user->exists()) {
            throw new RuntimeException(__("Cannot associate the collection with an unsaved user."));
        }
        $this->owner_id = $user->id;
    }
    
    /**
     * Set added and modified timestamps for the collection.
     */
    protected function beforeInsert()
    {
        $now = Zend_Date::now()->toString(self::DATE_FORMAT);
        $this->added = $now;
        $this->modified = $now;
        if (!$this->owner_id && ($user = Omeka_Context::getInstance()->getCurrentUser())) {
            $this->setAddedBy($user);
        }
    }
    
    /**
     * Set modified timestamp for the collection.
     */
    protected function beforeUpdate()
    {
        $this->modified = Zend_Date::now()->toString(self::DATE_FORMAT);
    }

    protected function beforeSave()
    {
        $boolFilter = new Omeka_Filter_Boolean();
        $this->featured = $boolFilter->filter($this->featured);
        $this->public = $boolFilter->filter($this->public);
    }
}
