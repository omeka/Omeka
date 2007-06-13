<?php

/**
 * Kea_EventListener
 *
 * @package Omeka
 * 
 **/
class Kea_EventListener extends Doctrine_EventListener
{
	private $plugin;
	
	public function __construct(Kea_Plugin $plugin) {
		$this->plugin = $plugin;
	}
	
	/**
	 * dispatch('onAdd', $item) --> calls plugin::onAddItem($item)
	 *
	 * @return void
	 **/
	protected function dispatch($event, $record) {
		$method = $event.get_class($record);
		if(method_exists($this->plugin, $method)) {
			call_user_func_array(array($this->plugin, $method), array($record));
		}
	}
	
	public function onLoad(Doctrine_Record $record) {
		$this->dispatch('onShow', $record);
	}
	
    public function onUpdate(Doctrine_Record $record) {
		$this->dispatch('onEdit', $record);
	}
 
    public function onInsert(Doctrine_Record $record) {
		$this->dispatch('onAdd', $record);
	}

    public function onPreDelete(Doctrine_Record $record) {
		$this->dispatch('onDelete', $record);
	}
} // END class Kea_EventListener

?>