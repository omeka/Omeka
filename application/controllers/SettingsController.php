<?php

require_once 'Kea/Controller/Action.php';
class SettingsController extends Kea_Controller_Action
{	
	public function indexAction() {
		$this->_forward('settings', 'edit');
	}
	
	public function browseAction() {
		$this->_forward('settings', 'edit');
	}
	
	public function editAction() {
		
		
		//Any changes to this list should be reflected in the install script (and possibly the view functions)		
		$settingsList = array(
			'site_title', 
			'copyright',
			'author', 
			'description', 
			'thumbnail_constraint', 
			'square_thumbnail_constraint',
			'fullsize_constraint', 
			'path_to_convert');
		
		$options = Zend::Registry( 'options' );
		
		foreach ($options as $k => $v) {
			if(in_array($k, $settingsList)) {
				$settings[$k] = $v;
			}
		}
				
		$optionTable = $this->getTable('Option')->getTableName();
		$conn = $this->getConn();
						
		//process the form
		if(!empty($_POST)) {
			$sql = "UPDATE $optionTable SET value = ? WHERE name = ?";
			foreach( $_POST as $key => $value )
			{
				if(array_key_exists($key,$settings)) {
					$value = get_magic_quotes_gpc() ? stripslashes( $value ) : $value;
					$conn->execute($sql, array($value, $key));
					$settings[$key] = $value;
					$options[$key] = $value;
				}
			}
			Zend::register('options', $options);
			$this->flash("Settings have been changed.");
		}

		$this->render('settings/edit.php', $settings);
	}
}

?>