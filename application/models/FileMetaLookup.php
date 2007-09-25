<?php
/**
 * FileMetaLookup
 * @package: Omeka
 */
class FileMetaLookup extends Omeka_Record
{
    public function setTableDefinition()
    {
		$this->option('type', 'MYISAM');
		$this->hasColumn('mime_type', 'string', 255, array('notblank'=>true));
		$this->hasColumn('table_name', 'string', 255, array('notblank'=>true));
		$this->hasColumn('table_class', 'string', 255, array('notblank'=>true));
		
		$this->index('mime_type', array('fields' => array('mime_type')));
    }
    public function setUp()
    {
		$this->hasMany('File as Files', 'File.lookup_id');
    }
}

?>