<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Represents a kind of mixin for Omeka_Record implementations.
 *
 * Any methods declared for an implementation of this class can be called
 * transparently by an Omeka_Record object that uses one of these modules.
 *
 * For instance, the Item model does not have an addTags() method, but the
 * Taggable class does.  Since Item declares Taggable as one of its modules,
 * an Item instance call all of Taggable's methods, so that adding tags
 * would be as simple as calling $item->addTags('foo, bar');
 *
 * Note that this is not a true mixin because it cannot override any existing
 * methods on a Record object.
 *
 * @see Taggable
 * @see Orderable
 * @see Omeka_Record
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
abstract class Omeka_Record_Mixin
{
    /**
     * Underlying record object.
     *
     * @var Omeka_Record
     */
    protected $record;

    /**
     * Delegate method calls to the underlying record.
     *
     * Convenience method to allow directly calling methods on the record
     * using $this.
     *
     * @param string $m Method name.
     * @param array $a Method arguments.
     * @return mixed
     */
    public function __call($m, $a)
    {
        return call_user_func_array( array($this->record, $m), $a);
    }

    /**#@+
     * Callback automatically called by Omeka_Record.
     *
     * See the corresponding {@link Omeka_Record} method for definitions of
     * call times.
     *
     * @return void
     */
    public function beforeSave() {}
    public function beforeUpdate() {}
    public function beforeInsert() {}
    public function afterInsert() {}
    public function afterSave() {}
    public function afterUpdate() {}
    /**
     * @param ArrayObject $post POST array. Passed by reference; changes to this
     * array will affect the caller's $post array.
     */
    public function afterSaveForm($post) {}
    /**
     * @param ArrayObject $post POST array. Passed by reference; changes to this
     * array will affect the caller's $post array.
     */
    public function beforeSaveForm($post) {}
    public function beforeDelete() {}
    public function afterDelete() {}
    public function beforeValidate() {}
    public function afterValidate() {}
    /**#@-*/
}
