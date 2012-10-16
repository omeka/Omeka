<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Represents a kind of mixin for Omeka_Record_AbstractRecord implementations.
 *
 * Any methods declared for an implementation of this class can be called
 * transparently by an Omeka_Record_AbstractRecord object that uses one of these 
 * modules.
 *
 * For instance, the Item model does not have an addTags() method, but the
 * Taggable class does.  Since Item declares Taggable as one of its modules,
 * an Item instance call all of Taggable's methods, so that adding tags would be 
 * as simple as calling $item->addTags('foo, bar');
 *
 * Note that this is not a true mixin because it cannot override any existing
 * methods on a Record object.
 * 
 * @package Omeka\Record\Mixin
 */
abstract class Omeka_Record_Mixin_AbstractMixin
{
    /**
     * Underlying record object.
     *
     * @var Omeka_Record_AbstractRecord
     */
    protected $_record;

    /**
     * Base mixin constructor.
     * 
     * Store the underlying record for use in the mixin.
     *
     * @param Omeka_Record_AbstractRecord $record
     */
    public function __construct($record)
    {
        $this->_record = $record;
    }

    /**#@+
     * Callback automatically called by Omeka_Record_AbstractRecord.
     *
     * See the corresponding {@link Omeka_Record_AbstractRecord} method for 
     * definitions of call times.
     *
     * @return void
     */
    public function beforeSave($args) {}
    public function afterSave($args) {}
    public function beforeDelete() {}
    public function afterDelete() {}
    /**#@-*/
}
