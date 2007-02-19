<?php
/* TODO: integrate searching of Tags and other models related to the Item
 *
 */
define('SEARCH_DIR', BASE_DIR.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'search');
require_once 'Zend/Search/Lucene.php';

/**
 * This will help us index things with the Zend_Search
 *
 * @package Omeka
 * @author Kris Kelly
 **/
class Kea_SearchListener extends Doctrine_EventListener
{
	private $index;
	
	protected $_keywordFields = array('added', 'modified', 'date');
	
	protected $_doNotIndex = array('password', 'Option', 'Group'); 
	
	public function __construct() {
		$create = !file_exists(SEARCH_DIR.DIRECTORY_SEPARATOR.'index.lock');
		$this->index = new Zend_Search_Lucene(SEARCH_DIR, $create);
		
		//We need this because Zend Search has some sort of retarded default that won't let you search through digits
		Zend_Search_Lucene_Analysis_Analyzer::setDefault( new Zend_Search_Lucene_Analysis_Analyzer_Common_TextNum_CaseInsensitive() );
	}
	
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
						$doc->addField(Zend_Search_Lucene_Field::Text($field, $record->$field));
					}				
				}
			}
			$doc->addField(Zend_Search_Lucene_Field::Keyword('model_name', get_class($record)));
			$this->index->addDocument($doc);
		}
	}

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
	
	public function onUpdate(Doctrine_Record $record) {
		$this->onPreDelete($record);
		$this->onInsert($record);
	}

} // END class SearchListener extends 

?>