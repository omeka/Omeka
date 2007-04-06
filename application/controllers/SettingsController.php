<?php

require_once 'Kea/Controller/Action.php';
class SettingsController extends Kea_Controller_Action
{	
	public function indexAction() {
		$this->_forward('settings', 'edit');
	}
	
	public function editAction() {
		$table = Doctrine_Manager::getInstance()->getTable('Option');
		
		//Any changes to this list should be reflected in the install script (and possibly the view functions)		
		$settings = array('site_title', 'copyright','meta_keywords', 'meta_author', 'meta_description', 'thumbnail_width', 'thumbnail_height', 'fullsize_width', 'fullsize_height', 'path_to_convert');
		
		foreach( $settings as $setting )
		{
			$$setting = $table->findBySQL("name LIKE '$setting'")->getFirst();
			if(!$$setting) {
				$$setting = new Option();
				$$setting->name = $setting;
				$$setting->value = "";
			}
		}
						
		//process the form
		if(!empty($_POST)) {
			$options = Zend::Registry('options');
			foreach( $_POST as $key => $value )
			{
				if(isset($$key)) {
					$value = get_magic_quotes_gpc() ? stripslashes( $value ) : $value;
					$$key->value = $value;
					$options[$key] = $value;
					$$key->save();
				}
			}
			Zend::register('options', $options);
			$this->flash("Settings have been changed.");
		}
		
		$this->render('settings/edit.php', compact($settings));
	}
}

?>