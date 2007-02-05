<?php
/* TODO: integrate searching of Tags and other models related to the Item
 *
 */
define('SEARCH_DIR', BASE_DIR.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'search');
require_once 'Zend/Search/Lucene.php';

/**
 * This will help us index things with the Zend_Search
 *
 * @package Sitebuilder
 * @author Kris Kelly
 **/
class Kea_SearchListener extends Doctrine_EventListener
{
	private $index;
	
	public function __construct() {
		$create = !file_exists(SEARCH_DIR.DIRECTORY_SEPARATOR.'index.lock');
		$this->index = new Zend_Search_Lucene(SEARCH_DIR, $create);
		
		//We need this because Zend Search has some sort of retarded default that won't let you search through digits
		Zend_Search_Lucene_Analysis_Analyzer::setDefault( new Zend_Search_Lucene_Analysis_Analyzer_Common_TextNum_CaseInsensitive() );
	}
	
	public function onInsert(Doctrine_Record $record) {
		Zend_Search_Lucene_Analysis_Analyzer::setDefault( new Zend_Search_Lucene_Analysis_Analyzer_Common_TextNum_CaseInsensitive() );
		if(get_class($record) == 'Item') {
			$doc = new Zend_Search_Lucene_Document();
			$doc->addField(Zend_Search_Lucene_Field::Keyword('added', $record->added));
			$doc->addField(Zend_Search_Lucene_Field::Keyword('modified', $record->modified));
			$doc->addField(Zend_Search_Lucene_Field::Text('title', $record->title));
			$doc->addField(Zend_Search_Lucene_Field::Text('description', $record->description));
			$doc->addField(Zend_Search_Lucene_Field::Text('publisher', $record->publisher));
			$doc->addField(Zend_Search_Lucene_Field::Text('relation', $record->relation));
			$doc->addField(Zend_Search_Lucene_Field::Text('coverage', $record->coverage));
			$doc->addField(Zend_Search_Lucene_Field::Text('rights', $record->rights));
			$doc->addField(Zend_Search_Lucene_Field::Text('subject', $record->subject));
			$doc->addField(Zend_Search_Lucene_Field::Text('source', $record->source));
			$doc->addField(Zend_Search_Lucene_Field::Text('creator', $record->creator));
			$doc->addField(Zend_Search_Lucene_Field::Text('additional_creator', $record->additional_creator));
			$doc->addField(Zend_Search_Lucene_Field::Text('language', $record->language));
			$doc->addField(Zend_Search_Lucene_Field::Keyword('date', $record->date));
			$doc->addField(Zend_Search_Lucene_Field::Keyword('item_id', $record->id));
			$this->index->addDocument($doc);
		}
	}
	
	public function onPreDelete(Doctrine_Record $record) {
		if(get_class($record) == 'Item') {
			Zend_Search_Lucene_Analysis_Analyzer::setDefault( new Zend_Search_Lucene_Analysis_Analyzer_Common_TextNum_CaseInsensitive() );
			$term = new Zend_Search_Lucene_Index_Term($record->id, 'item_id');
			$query = new Zend_Search_Lucene_Search_Query_Term($term);
			$hits = $this->index->find($query);
			$this->index->delete($hits[0]);
		}
	}
	
	public function onUpdate(Doctrine_Record $record) {
		$this->onDelete($record);
		$this->onInsert($record);
	}

} // END class SearchListener extends 

?>