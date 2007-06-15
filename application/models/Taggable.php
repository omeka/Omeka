<?php
/**
 * Taggable
 * Adaptation of the Rails Acts_as_taggable
 * @package: Omeka
 */
class Taggable
{
	protected $record;
	protected $joinClass;
	
	public function __construct(Kea_Record $record) {
		$this->record = $record;
		
		//e.g. ExhibitsTags
		$this->joinClass = get_class($record) . 'sTags';
		
		//e.g. exhibit_id
		$this->joinId = strtolower(get_class($record)) . '_id';
		
		$this->tagTable = Zend::Registry('doctrine')->getTable('Tag');
		$this->tagTableName = $this->tagTable->getTableName();
		
		$this->joinTable = Zend::Registry('doctrine')->getTable($this->joinClass)->getTableName();
		
		$this->conn = Doctrine_Manager::getInstance()->connection();
	}
	
	protected function pluginHook($hookName, $varsToPass = array())
	{
		$broker = Kea_Controller_Plugin_Broker::getInstance();
		
		call_user_func_array(array($broker, $hookName), $varsToPass);
	}
	
	public function getTagJoinTableName()
	{
		return $this->joinTable;
	}
	
	protected function refreshTags()
	{
		//Reload the instances of the join class
		$dql = "SELECT j.* FROM {$this->joinClass} j WHERE j.{$this->joinId} = ?";
		$q = new Doctrine_Query;
		
		$joinClass = $this->joinClass;
		$this->record->$joinClass = $q->parseQuery($dql)->execute(array($this->record->id));
		
		//Reload the instances of the Tag class
		$dql = "SELECT t.* FROM Tag t INNER JOIN t.{$this->joinClass} j WHERE j.{$this->joinId} = ?";
		$this->record->Tags = $q->parseQuery($dql)->execute(array($this->record->id));
	}
	
	public function userTags($user_id)
	{
		if($user_id instanceof User) {
			$user_id = $user_id->id;
		}
		$table = Zend::Registry('doctrine')->getTable('Tag');
		
		$dql = "SELECT t.* FROM Tag t INNER JOIN t.{$this->joinClass} j WHERE j.user_id = ? AND j.{$this->joinId} = ?";
		
		$q = new Doctrine_Query;
		
		$tags = $q->parseQuery($dql)->execute(array($user_id, $this->record->id));
				
		return $tags;
	}
	
	public function getTagCount($tagName)
	{
		$dql = "SELECT COUNT(j.id) count FROM {$this->joinClass} j INNER JOIN j.Tag t WHERE j.{$this->joinId} = ? AND t.name = ?";
		
		$q = new Doctrine_Query;
		$res = $q->parseQuery($dql)->execute(array($this->record->id, trim($tagName)));
		
		return $res[0]->count;
	}

	
	protected function insertJoin($tag_id, $user_id, $join_id) {
				
		$sql = "INSERT IGNORE INTO {$this->joinTable} (tag_id, user_id, {$this->joinId}) VALUES (?, ?, ?)";
		
		$res = $this->conn->execute($sql, array($tag_id, $user_id, $join_id));
		return ($res->rowCount() > 0);
	}	
	
	protected function deleteJoin($tag_id, $user_id, $join_id) {		
		$sql = "DELETE IGNORE FROM {$this->joinTable} WHERE tag_id = ? AND user_id = ? AND {$this->joinId} = ? LIMIT 1";
		
		$res = $this->conn->execute($sql, array($tag_id, $user_id, $join_id));
		return ($res->rowCount() > 0);
	}	
	
	protected function deleteTagForRecord($tag_id, $join_id) {
		$sql = "DELETE FROM {$this->joinTable} WHERE tag_id = ? AND {$this->joinId} = ?";
		$res = $this->conn->execute($sql, array($tag_id, $join_id));
		return ($res->rowCount() > 0);
	}
	
	protected function insertTag($tagName) {
		$sql = "INSERT INTO {$this->tagTable} (name) VALUES (?)";
		$res = $this->conn->execute($sql, array(trim($tagName)));
		return ($res->rowCount() > 0);
	}
	
	protected function getTagId($name) {
		$sql = "SELECT t.id FROM {$this->tagTableName} t WHERE t.name LIKE ?";
		$res = $this->conn->execute($sql, array($name))->fetch();
		return $res[0];
	}
	
	/**
	 * Delete a tag from the record
	 *
	 * @param int userId The user to specifically delete the tag from
	 * @param bool deleteAll Whether or not to delete all references to this tag for this record
	 * @return bool|array Whether or not the tag was deleted (false if tag doesn't exist)
	 **/
	public function deleteTag($tagName, $userId=null, $deleteAll=false)
	{
		$joinType = $this->joinClass;
		
		if($tagName instanceof Tag) {
			$tagName = $tagName->name;
		}
		
		if(is_array($tagName)) {
			foreach ($tagName as $key => $t) {
				$wasDeleted[$key] = $this->deleteTag($t);
			}
			return $wasDeleted;
		}
		else {
			$tagObj = $this->tagTable->findByName($tagName);
			if(!$tagObj) {
				return false;
			}
			
			if($deleteAll) {
				return $this->deleteTagForRecord($tagObj->id, $this->record->id);	
			}
			elseif(is_numeric($userId)) {
				return $this->deleteJoin($tagObj->id, $userId, $this->record->id);
			}
			
			
		}
	}
	
	/** If the $tag were a string and the keys of Tags were just the names of the tags, this would be:
	 * in_array(array_keys($this->Tags))
	 *
	 * @return boolean
	 **/
	public function hasTag($tag, $user=null) {
		$q = new Doctrine_Query;
		$q->parseQuery("SELECT COUNT(j.id) count FROM {$this->joinClass} j INNER JOIN j.Tag t");
		
		$tagName = ($tag instanceof Tag) ? $tag->name : $tag;
		$q->addWhere("t.name = ? AND j.{$this->joinId} = ?", array($tagName, $this->record->id));

		if($user)
		{
			if($user instanceof User)
			{
				if(!$user->exists()) return false;
				$user = $user->id;
			}
			$q->addWhere('j.user_id = ?', array($user));
		}

		$res = $q->execute();
		return ($res[0]->count > 0);
	}	
		
	public function tagString($wrap = null, $delimiter = ',') {
		$string = '';
		foreach( $this->record->Tags as $key => $tag )
		{
			if($tag->exists()) {
				$name = $tag->__toString();
				$string .= (!empty($wrap) ? preg_replace("/$name/", $wrap, $name) : $name);
				$string .= ( ($key+1) < $this->record->Tags->count() ) ? $delimiter.' ' : '';
			}
		}
		
		return $string;
	}

	public function addTags($tags, $user_id, $delimiter = ',') {
		if(!$this->record->id) {
			throw new Exception( 'A valid record ID # must be provided when tagging.' );
		}
		
		if(!$user_id) {
			throw new Exception( 'A valid user ID # must be provided when tagging' );
		}
		
		if(!is_array($tags)) {
			$tags = explode($delimiter, $tags);
			$tags = array_diff($tags, array(''));
		}
		
		foreach ($tags as $key => $tagName) {
			$tag_id = $this->getTagId($tagName);
			
			if(!$tag_id) {
				$sql = "INSERT INTO {$this->tagTableName} (name) VALUES (?)";
				$res = $this->conn->execute($sql,array($tagName));
				$tag_id = $this->conn->getDbh()->lastInsertId();
			}
			 
			$this->insertJoin($tag_id, $user_id, $this->record->id);
		}
	}

	/**
	 * Calculate the difference between a tag string and a set of tags
	 *
	 * @return array Keys('removed','added')
	 **/
	public function diffTagString( $string, $tags=null, $delimiter=",")
	{
		if(!$tags)
		{
			$tags = $this->record->Tags;
		}
		
		$inputTags = array();
		$existingTags = array();
		$inputTags = explode($delimiter,$string);
		
		foreach ($inputTags as $key => $inputTag) {
			$inputTags[$key] = trim($inputTag);
		}
		
		//get rid of empty tags
		$inputTags = array_diff($inputTags,array(''));
		
		foreach ($tags as $key => $tag) {
			if($tag instanceof Tag || is_array($tag)) {
				$existingTags[$key] = trim($tag["name"]);
			}else{
				$existingTags[$key] = trim($tag);
			}
			
		}
		if(!empty($existingTags)) {
			$removed = array_values(array_diff($existingTags,$inputTags));
		}
		
		if(!empty($inputTags)) {
			$added = array_values(array_diff($inputTags,$existingTags));
		}
		return compact('removed','added');
	}	

	/**
	 * This will add tags that are in the tag string and remove those that are no longer in the tag string
	 *
	 * @return void
	 **/
	public function applyTagString($string, $user_id, $deleteTags = false, $delimiter=",")
	{
		$tags = ($deleteTags) ? $this->record->Tags : $this->userTags($user_id);
		$diff = $this->diffTagString($string, $tags, $delimiter);
		
		if(!empty($diff['removed'])) {
			$this->removeTagsByArray($diff['removed'], $user_id, $deleteTags);
			
			//PLUGIN HOOKS
			$this->pluginHook('onUntag' . get_class($this->record), array($this->record, $diff['removed'], $user_id));
		}
		if(!empty($diff['added'])) {
			$this->addTags($diff['added'], $user_id);
			
			//PLUGIN HOOKS
			$this->pluginHook('onTag' . get_class($this->record), array($this->record, $diff['added'], $user_id));
		}
		
	}
	
	public function removeTagsByArray($array,$user_id, $deleteWholeTag=false)
	{
		/*
		 *	Process for deleting the whole tag
		 *		1) delete all references for that tag/record combo within the relevant join table
		 *		2) check to see if there are any other references in that or the other join tables
		 *		3) if there are no references in the other join tables, delete the entry from the tag table
		 *
		 *		Right now this only does 1), not sure of the best way to determine which references a tag contains
		 */		
		foreach ($array as $tagName) {
			if($this->deleteTag($tagName, $user_id, $deleteWholeTag)) {
				$success = true;
			}
		}
		
		if($success) {
				$this->refreshTags();
		}	
				
	}

}

?>