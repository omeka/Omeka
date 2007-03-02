<?php

require_once 'Kea/Controller/Action.php';
class SettingsController extends Kea_Controller_Action
{
	public function browseAction() {
		$this->render('settings/browse.php');
	}
	
	public function editAction() {
		$this->render('settings/edit.php');
	}
}

?>