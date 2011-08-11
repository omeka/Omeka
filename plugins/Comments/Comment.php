<?php
include '../../paths.php';
require_once '../../application/libraries/Omeka/Context.php';
require_once '../../application/libraries/Zend/Application.php';
require_once '../../application/libraries/Omeka/Core.php';
require_once 'BaseEntity.php';

/**
 * @version $Id$
 * @copyright Scand Ltd.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @author Scand Ltd.
 **/

class Comment extends BaseEntity
{
    private $_item_id, $_description, $_guest_name, $_guest_ip, $_added, $_rate;


	/**
     * @return the $_rate
     */
    public function get_rate ()
    {
        return $this->_rate;
    }

	/**
     * @param field_type $_rate
     * @return Comment
     */
    public function set_rate ($_rate)
    {
        $this->_rate = $_rate;
        return $this;
    }

	/**
     * @return the $_item_id
     */
    public function get_item_id ()
    {
        return $this->_item_id;
    }

	/**
     * @return the $_description
     */
    public function get_description ()
    {
        return $this->_description;
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
     * @return Comment
     */
    public function set_item_id ($_item_id)
    {
        $this->_item_id = $_item_id;
        return $this;
    }

	/**
     * @param field_type $_description
     * @return Comment
     */
    public function set_description ($_description)
    {
        $this->_description = $_description;
        return $this;
    }

	/**
     * @param field_type $_guest_name
     * @return Comment
     */
    public function set_guest_name ($_guest_name)
    {
        $this->_guest_name = $_guest_name;
        return $this;
    }

	/**
     * @param field_type $_guest_ip
     * @return Comment
     */
    public function set_guest_ip ($_guest_ip)
    {
        $this->_guest_ip = $_guest_ip;
        return $this;
    }

	/**
     * @param field_type $_added
     * @return Comment
     */
    public function set_added ($_added)
    {
        $this->_added = $_added;
        return $this;
    }
    
	/**
     * Get comments by item_id
     * @param unknown_type $item_id
     * @return Comment array
     */
    public static function getByItemId($item_id)
    {
        $comment = new self();
        $res = $comment->getDb()->exec("select * from omeka_comments where item_id=?", array($item_id));
        $result = array();
        while (($row = $res->fetch()) !== false)
        {
            $result[] = new Comment($row);
        }
        return $result;
    }
    
    /**
     * Populate Comment by id
     * @see BaseEntity::populate()
     * @return true if populated
     */
    public function populate($id)
    {
        $res = $this->getDb()->exec("select * from omeka_comments where id=?", array($id));
        if (($row = $res->fetch()) !== false)
        {
            $this->populateFromArray($row);
            return true;
        }
        return false;
    }
    
    /**
     * Get rates of comments (ids)
     * @param unknown_type $item_ids
     * @return array[comment_id] = rate
     */
    public static function getRates($item_ids = array())
    {
        $comment = new self();
        $res = $comment->getDb()->exec("select avg(rate) as rate, item_id from omeka_comments where item_id in (".implode(',', $item_ids).") group by item_id", array());
        $result = array();
        while (($row = $res->fetch()) !== false)
            $result[$row['item_id']] = round($row['rate'],2);
        return $result;
    }
    
    /**
     * Add Comment
     * @see BaseEntity::save()
     * return true if ok
     */
    public function save()
    {
        if ($this->get_id())
            throw new Exception('Comment can not be updated', 1);
        $this->validate();
        $sql = "insert into omeka_comments (item_id, guest_name, guest_ip, added, description, rate) values (?, ?, ?, now(), ?, ?)";
        $res = $this->getDb()->exec($sql, array(
            $this->get_item_id()
            , $this->get_guest_name()
            , $this->get_guest_ip()
            , $this->get_description()
            , $this->get_rate()
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
        if ($this->isEmpty($this->get_description()))
            throw new Exception('Description can not be empty', 101);
        if ($this->isEmpty($this->get_rate()))
            throw new Exception('Rate can not be empty', 102);
        if ($this->isEmpty($this->get_item_id()))
            throw new Exception('Comment is not correct', 103);
        return true;
    }
    
    private function isEmpty($v)
    {
        return empty($v);
    }
    
    
}