<?php
/**
 * Equivalent of DB trigger to update timestamps on insert/update of records
 *
 * @author Kris Kelly
 **/
class Kea_TimestampListener extends Doctrine_EventListener
{
	public function onPreInsert(Doctrine_Record $record) {
		$date = date('YmdHis');
		if($record->hasRelation('added')) {
			$record->added = $date;
		}
		if($record->hasRelation('modified')) {
			$record->modified = $date;
		}
		
	}
	
	public function onPreUpdate(Doctrine_Record $record) {
		$date = date('YmdHis');
		if($record->hasRelation('modified')) {
			$record->modified = $date;
		}
	}
} // END class Kea_TimestampListener

?>