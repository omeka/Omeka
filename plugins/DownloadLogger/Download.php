<?php
require_once 'BaseEntity.php';

/**
 * @version $Id$
 * @copyright Scand Ltd.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @author Scand Ltd.
 **/

class Download extends BaseEntity
{
    private $_item_id, $_guest_name, $_guest_ip, $_added;
	/**
     * @return the $_item_id
     */
    public function get_item_id ()
    {
        return $this->_item_id;
    }

	/**
     * @return the $_guest_name
     */
    public function get_guest_name ()
    {
        return $this->_guest_name;
    }

	/**
     * @return the $_guest_ip
     */
    public function get_guest_ip ()
    {
        return $this->_guest_ip;
    }

	/**
     * @return the $_added
     */
    public function get_added ()
    {
        return $this->_added;
    }

	/**
     * @param field_type $_item_id
     * @return Download
     */
    public function set_item_id ($_item_id)
    {
        $this->_item_id = $_item_id;
        return $this;
    }

	/**
     * @param field_type $_guest_name
     * @return Download
     */
    public function set_guest_name ($_guest_name)
    {
        $this->_guest_name = $_guest_name;
        return $this;
    }

	/**
     * @param field_type $_guest_ip
     * @return Download
     */
    public function set_guest_ip ($_guest_ip)
    {
        $this->_guest_ip = $_guest_ip;
        return $this;
    }

	/**
     * @param field_type $_added
     * @return Download
     */
    public function set_added ($_added)
    {
        $this->_added = $_added;
        return $this;
    }

    /**
     * Add Download
     * @see BaseEntity::save()
     * return true if ok
     */
    public function save()
    {
        if ($this->get_id())
            throw new Exception('Download can not be updated', 1);
        $this->validate();
        $sql = "insert into omeka_download (item_id, guest_name, guest_ip, added) values (?, ?, ?, now())";
        $res = $this->getDb()->exec($sql, array(
            $this->get_item_id()
            , $this->get_guest_name()
            , $this->get_guest_ip()
            ));
        $this
            ->set_added(date('Y-m-d H:i:s'))
            ->set_id((int) $this->getDb()->lastInsertId());
        return true;
    }
    
    /**
     * Simple validation
     * @see BaseEntity::validate()
     * @return true if ok
     */
    public function validate()
    {
        if ($this->isEmpty($this->get_item_id()))
            throw new Exception('Download is not correct', 103);
        return true;
    }
    
    private function isEmpty($v)
    {
        return empty($v);
    }
    
    
    /**
     * Populate Download by id
     * @see BaseEntity::populate()
     * @return true if populated
     */
    public function populate($id)
    {
        $res = $this->getDb()->exec("select * from omeka_download where id=?", array($id));
        if (($row = $res->fetch()) !== false)
        {
            $this->populateFromArray($row);
            return true;
        }
        return false;
    }
    
    
	/**
     * Get comments by item_id
     * @param unknown_type $item_id
     * @return Download array
     */
    public static function getByItem(Item $item)
    {
        $comment = new self();
        $res = $comment->getDb()->exec("select * from omeka_download where item_id=?", array($item->id));
        $result = array();
        while (($row = $res->fetch()) !== false)
        {
            $result[] = new Download($row);
        }
        return $result;
    }
    
    
}