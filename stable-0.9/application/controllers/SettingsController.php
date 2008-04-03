<?php

require_once 'Omeka/Controller/Action.php';
class SettingsController extends Omeka_Controller_Action
{	
	public function indexAction() {
		$this->_forward('edit', 'settings');
	}
	
	public function browseAction() {
		$this->_forward('edit', 'settings');
	}
	
	public function editAction() {
		
		
		//Any changes to this list should be reflected in the install script (and possibly the view functions)		
		$settingsList = array(
			'site_title', 
			'copyright',
			'administrator_email',
			'author', 
			'description', 
			'thumbnail_constraint', 
			'square_thumbnail_constraint',
			'fullsize_constraint', 
			'path_to_convert');
		
		$options = Zend_Registry::get('options');
		
		foreach ($options as $k => $v) {
			if(in_array($k, $settingsList)) {
				$settings[$k] = $v;
			}
		}
				
		$optionTable = $this->getTable('Option')->getTableName();
		$conn = get_db();
						
		//process the form
		if(!empty($_POST)) {
			$sql = "UPDATE $optionTable SET value = ? WHERE name = ?";
			foreach( $_POST as $key => $value )
			{
				if(array_key_exists($key,$settings)) {
					$value = get_magic_quotes_gpc() ? stripslashes( $value ) : $value;
					$conn->exec($sql, array($value, $key));
					$settings[$key] = $value;
					$options[$key] = $value;
				}
			}
			Zend_Registry::set('options', $options);

			$this->flash("Settings have been changed.");
		}

		$this->render('settings/edit.php', $settings);
	}
}

?>