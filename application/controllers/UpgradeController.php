<?php 
/**
*  Current migration # is passed as an invoke argument to this controller
*/
class UpgradeController extends Omeka_Controller_Action
{
	/**
	 * Silence all exceptions that might occur when activating this controller, 
	 * since all of these exceptions would come from having an outdated database anyway
	 *
	 **/
	public function __construct($req, $resp, $invoke=array())
	{
		try {
			parent::__construct($req, $resp, $invoke);
		} catch (Exception $e) {}
		
		$this->_view = new Omeka_View($this);
		$this->_view->addScriptPath(CORE_DIR . DIRECTORY_SEPARATOR . 'templates');
		$this->_view->addAssetPath(
		    CORE_DIR . DIRECTORY_SEPARATOR . 'templates', 
		    WEB_ROOT . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'templates');
		require_once HELPERS;		
	}
	
	/**
	 * Check to see if the migration is at the right #, then allow or disallow further access
	 *
	 * @return void
	 **/
	public function init()
	{	    
	    //Test to see whether we need to upgrade Omeka or not  
		if(!$this->migrationIsNecessary()) {
		    $this->getRequest()->setActionName('completed');
		}
	}
	
	/**
	 * Quick way to determine whether or not it's necessary to migrate the data
	 *
	 * @return void
	 **/
	protected function migrationIsNecessary()
	{
	    $startMigration = $this->getStartMigration();
  
	    $endMigration = (int) OMEKA_MIGRATION;
	    
	    return ($startMigration < $endMigration);
	}
	
	protected function getStartMigration()
	{
	    return (int) get_option('migration');
	}
	
	/**
	 * Run the migration script, obtain any success/error output and display it in a pretty way
	 *
	 * @return void
	 **/
	public function migrateAction()
	{
	    //If we've already migrated the database, somebody is trying to pass bad data
	    if(!$this->migrationIsNecessary()) {
	        return $this->completedAction();
	    }
	    
    	require_once 'Omeka/Upgrader.php';
    	$upgrader = new Omeka_Upgrader($this->getStartMigration(), OMEKA_MIGRATION);	   
    	$upgrader->run(); 
    	
    	$output = $upgrader->getOutput();
    	$errors = $upgrader->getErrors();
    	
    	$success = (bool) !count($errors);
    	
    	$this->render('upgrade/status.php', compact('output','errors', 'success'));
	}
	
	public function completedAction()
	{   
	    $this->render('upgrade/completed.php');	    
	}
	
	/**
	 * Simplified version of the parent::render() method
	 *
	 * @return void
	 **/
	public function render($file, $vars = array())
	{
	    $this->_view->assign($vars);
	    $body = $this->_view->render($file);
	    $this->getResponse()->appendBody($body);
	}
}
 
?>
