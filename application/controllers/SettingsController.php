<?php

require_once 'Kea/Controller/Action.php';
class SettingsController extends Kea_Controller_Action
{	
	public function indexAction() {
		$this->_forward('settings', 'edit');
	}
	
	public function editAction() {
		$table = $this->getTable('Option');
		
		//Any changes to this list should be reflected in the install script (and possibly the view functions)		
		$settingsList = array(
			'site_title', 
			'copyright',
			'meta_keywords', 
			'meta_author', 
			'meta_description', 
			'thumbnail_constraint', 
			'fullsize_constraint', 
			'path_to_convert');
		
		foreach( $settingsList as $setting )
		{
			$option = $table->findBySQL("name LIKE '$setting'")->getFirst();
			if(empty($option)) {
				$option = new Option();
				$option->name = $setting;
				$option->value = "";
			}
			$settings[$setting] = $option;
		}
						
		//process the form
		if(!empty($_POST)) {
			$options = Zend::Registry('options');
			foreach( $_POST as $key => $value )
			{
				if(array_key_exists($key,$settings)) {
					$value = get_magic_quotes_gpc() ? stripslashes( $value ) : $value;
					$settings[$key]->value = $value;
					$settings[$key]->save();
					
					$options[$key] = $value;
				}
			}
			Zend::register('options', $options);
			$this->flash("Settings have been changed.");
		}
		
		foreach ($settings as $name=>$option) {
			$settings[$name] = $option->value;
		}

		$this->render('settings/edit.php', $settings);
	}
}

?>