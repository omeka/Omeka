<?php 
/**
* 
*/
abstract class Omeka_View_Format_Abstract
{
	protected $vars;
	protected $view;
	protected $options;
	
	/**
	 * These separate output formats need to know about the View object, 
	 * they need no explicit knowledge of either Request or Response
	 *
	 * @return void
	 **/
	public function __construct(Omeka_View $view, $options=array())
	{
		$this->view = $view;
		$this->options = $options;
	}
	
	protected function getView()
	{
		return $this->view;
	}
	
	protected function getRecord()
	{
		if($record = $this->getView()->record) {
			return $record;
		}
	}
	
	protected function getRecordset()
	{
		if($recordset = $this->getView()->recordset) {
			return $recordset;
		}
	}
	
	protected function getFile()
	{
		if(($file = $this->options['feed_filename'])) {
			return $file;
		}
	}
	
	/**
	 * Default behavior of these View Format wrapper classes is to attempt to render a 
	 * set of records (or single record) but fail if that data is not available
	 *
	 * This checks to see whether or not the class can actually render some data
	 *
	 * @see Omeka_View::getFormat()
	 * @return bool
	 **/
	public function canRender()
	{
		$view = $this->getView();
		return (isset($view->recordset) or isset($view->record));
//		return ((bool) $this->getRecord() or (bool) $this->getRecordset());
	}
	
	protected function hasRecord()
	{
		$view = $this->getView();
		return isset($view->record);
	}
	
	protected function hasRecordset()
	{
		$view = $this->getview();
		return isset($view->recordset);
	}
	
	protected function renderRecords()
	{
		if($this->hasRecord() and ($record = $this->getRecord())) {
			$class = get_class($record);
			$renderer = self::getModelOutputHandler($class, $this->getFormat());
			return $renderer->renderOne($record);		
		}elseif($this->hasRecordset()) {
			$class = $this->getView()->record_type;
			if(!$class) {
				throw new Omeka_View_Format_Exception( 'Class must be specified when rendering a set of records!' );
			}	
			if( !($records = $this->getRecordset())) {
				$records = array();
			}		
			$renderer = self::getModelOutputHandler($class, $this->getFormat());
			return $renderer->renderall($records);
		}else {
			throw new Omeka_View_Format_Exception( 'There are no records to render!' );
		}
	}
	
	/**
	 * Use a crude form of introspection to get the class name of the output format
	 *
	 * @return string (e.g. 'Rss', 'Atom', 'Json', etc.)
	 **/
	protected function getFormat()
	{
		return end(explode('_', get_class($this)));
	}
	
	protected function getCached() {}
	protected function setCached() {}
	
	/**
	 * B/c there is a wide range of potential output behaviors (RSS, ATOM, RDF, POX, JSON, ...)
	 * Individual models should be decoupled from their output behavior.
	 *
	 * This is a factory that takes care of returning the proper output class instance. 
	 * (which is a decorator for model objects/collections)
	 * 
	 * Classes that implement a specific output style for records should follow the naming convention:
	 * RecordClass + OutputAcronym (e.g, RSS for Items would be a class called ItemRSS)
	 *
	 * And they must be an instance of Omeka_Record_Output_Abstract
	 *
	 * Note that none of these classes has control over what data is retrieved for the data feed
	 * That information is left up to the controller.
	 *
	 * Line of control passes --> Omeka_Controller_Action --> Omeka_View --> Omeka_View_Format_Abstract --> Omeka_Record_Output_Abstract
	 * 
	 * 
	 * @return Omeka_Record_Output_Abstract
	 **/
	protected static function getModelOutputHandler($record_class, $output_format)
	{				
		$output_class = $record_class . ucwords($output_format);
		
		//i.e $output_class = "ItemXml"
		if(!class_exists($output_class)) {
			require_once $output_class . ".php";
		}
		
		$model_feed = new $output_class;
		
		return $model_feed;
	}
		
	/**
	 * Gets called by Omeka_View::render()
	 *
	 * @return void
	 **/
	public function render()
	{
		$output = $this->getCached();
		
		if(!$output) {
			$output = $this->_render();
		}
	
		//Check for a set of records if the custom method doesn't return anything
		if($output === null) {
			$output = $this->renderRecords();
		}
		
		$this->setCached($output);
				
		return $output;
	}
	
	abstract protected function _render();	
	
	protected function setHeader($name, $value=null) 
	{
		$this->getView()->getResponse()->setHeader($name, $value);
	}
}
 
?>
