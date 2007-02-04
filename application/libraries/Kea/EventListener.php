<?php

/**
 * Kea_EventListener
 *
 * @package SiteBuilder
 * @author Kris Kelly
 **/
class Kea_EventListener extends Doctrine_EventListener
{
	public function onLoad(Doctrine_Record $record) {
//        Kea_Plugin_Manager::getInstance()->notify('onLoadRecord', $record, null);
    }
    public function onPreLoad(Doctrine_Record $record) {
//		Kea_Plugin_Manager::getInstance()->notify('onPreLoadRecord', $record, null);
	}
    public function onUpdate(Doctrine_Record $record) {
//		Kea_Plugin_Manager::getInstance()->notify('onUpdateRecord', $record, null);
	}
    public function onPreUpdate(Doctrine_Record $record) {
//		Kea_Plugin_Manager::getInstance()->notify('onPreUpdateRecord', $record, null);
	}

    public function onCreate(Doctrine_Record $record) {
//		Kea_Plugin_Manager::getInstance()->notify('onCreateRecord', $record, null);
	}
    public function onPreCreate(Doctrine_Record $record) {}
 
    public function onSave(Doctrine_Record $record) {
		Kea_Plugin_Manager::getInstance()->notify('onSaveRecord', $record, null);
	}
    public function onPreSave(Doctrine_Record $record) {
		Kea_Plugin_Manager::getInstance()->notify('preSaveRecord', $record, null);
	}
 
    public function onInsert(Doctrine_Record $record) {}
    public function onPreInsert(Doctrine_Record $record) {}
 
    public function onDelete(Doctrine_Record $record) {
		Kea_Plugin_Manager::getInstance()->notify('onDeleteRecord', $record, null);
	}
    public function onPreDelete(Doctrine_Record $record) {}
 
    public function onEvict(Doctrine_Record $record) {}
    public function onPreEvict(Doctrine_Record $record) {}
 
    public function onSleep(Doctrine_Record $record) {}
    
    public function onWakeUp(Doctrine_Record $record) {}
    
    public function onClose(Doctrine_Connection $connection) {}
    public function onPreClose(Doctrine_Connection $connection) {}
    
    public function onOpen(Doctrine_Connection $connection) {}
 
    public function onTransactionCommit(Doctrine_Connection $connection) {}
    public function onPreTransactionCommit(Doctrine_Connection $connection) {}
 
    public function onTransactionRollback(Doctrine_Connection $connection) {}
    public function onPreTransactionRollback(Doctrine_Connection $connection) {}
 
    public function onTransactionBegin(Doctrine_Connection $connection) {}
    public function onPreTransactionBegin(Doctrine_Connection $connection) {}
    
    public function onCollectionDelete(Doctrine_Collection $collection) {}
    public function onPreCollectionDelete(Doctrine_Collection $collection) {}
} // END class Kea_EventListener

?>