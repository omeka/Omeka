<?php
define('SEARCH_DIR', BASE_DIR.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'search');
require_once 'Zend/Search/Lucene.php';

/**
 * This will help us index things with the Zend_Search
 *
 * @todo optimization
 * @package Omeka
 * 
 **/
class Kea_SearchListener extends Doctrine_EventListener
{
	private $index;
	
	protected $_keywordFields = array('added', 'modified', 'date');
	
	/**
	 * Array of fields/record types to exclude from indexing
	 *
	 * @var string
	 **/
	protected $_doNotIndex = array('password', 'Option', 'Group', 'ItemsTags', 'TypesMetafields', 'Plugin', 'GroupsPermissions'); 
	
	/**
	 * Initialize the index, if it doesn't exist then we rebuild it from the database
	 * I've noticed a bug (probably with Zend) where it doesn't immediately make the results of the rebuilt index searchable,
	 * but if you add something later then everything is searchable again.
	 *
	 * @return void
	 **/
	public function __construct() {
		$create = !file_exists(SEARCH_DIR.DIRECTORY_SEPARATOR.'index.lock');
		$this->index = new Zend_Search_Lucene(SEARCH_DIR, $create);
		if ( $create || $this->index->count() == 0)
		{
			$this->rebuildIndex();
		}	
				
		//We need this because Zend Search has some sort of retarded default that won't let you search through digits
		Zend_Search_Lucene_Analysis_Analyzer::setDefault( new Zend_Search_Lucene_Analysis_Analyzer_Common_TextNum_CaseInsensitive() );
	}
	
	/**
	 * If the index gets dropped or is somehow inaccurate, we want to be able to rebuild it from scratch
	 * Step 1 is delete the files manually from application/search, though we could do this automatically if necessary
	 * 
	 * @todo Optimize with Zend_Search_Lucene::setMergeFactor()
	 * @return void
	 **/
	protected function rebuildIndex() {
		require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Item.php';
		$toIndex = array('Item', 'Tag', 'Metafield', 'Collection', 'Type', 'User', 'Metatext');
		
		foreach( $toIndex as $className )
		{
			$records = Doctrine_Manager::getInstance()->getTable($className)->findAll();
			foreach( $records as $record )
			{
				$this->onInsert($record);
			}
			$this->index->optimize();
		}
	}
	
	/**
	 * Runs automagically every time a record is inserted into db, adds that record into the index
	 *
	 * Documents in the index always include the name of the model that the record derives from
	 * 
	 * @return void
	 **/
	public function onInsert(Doctrine_Record $record) {
		if(!in_array(get_class($record), $this->_doNotIndex)) {
			$doc = new Zend_Search_Lucene_Document();
			$columns = $record->getTable()->getColumns();
			foreach( $columns as $field => $value )
			{
				if(!empty($record->$field)) {
					if(in_array($field, $this->_keywordFields)) {
						$doc->addField(Zend_Search_Lucene_Field::Keyword($field, $record->$field));
					}elseif(!in_array($field, $this->_doNotIndex)){
						
						//If its a boolean value, store it as true/false
						if($value[0] == 'boolean') {
							$store = ($record->$field) ? 'TRUE' : 'FALSE';
						}else {
							$store = $record->$field;
						}
						$doc->addField(Zend_Search_Lucene_Field::Text($field, $store));
					}				
				}
			}
			$doc->addField(Zend_Search_Lucene_Field::Keyword('model_name', get_class($record)));
			$this->index->addDocument($doc);
		}
	}
	
	/**
	 * Deletes a record from the index whenever a record gets deleted from the db
	 *
	 * @return void
	 **/
	public function onPreDelete(Doctrine_Record $record) {
		Zend_Search_Lucene_Analysis_Analyzer::setDefault( new Zend_Search_Lucene_Analysis_Analyzer_Common_TextNum_CaseInsensitive() );
		$idTerm = new Zend_Search_Lucene_Index_Term($record->id, 'id');
		$modelTerm = new Zend_Search_Lucene_Index_Term(get_class($record), 'model_name');
		$query = new Zend_Search_Lucene_Search_Query_MultiTerm();
		$query->addTerm($modelTerm, true);
		$query->addTerm($idTerm, true);
		$hits = $this->index->find($query);
		if($hits) {
			$this->index->delete($hits[0]);
		}
	}
	
	/**
	 * Documents in the index cannot be updated, must be deleted and re-added
	 *
	 * @return void
	 **/
	public function onUpdate(Doctrine_Record $record) {
		$this->onPreDelete($record);
		$this->onInsert($record);
	}

} // END class SearchListener extends 

?>