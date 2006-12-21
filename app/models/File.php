<?php

class File extends Kea_Domain_Record { 
    public function setTableDefinition() {
		$this->setTableName('files');
		
        $this->hasColumn("title","string",400);
		$this->hasColumn("publisher","string",400);
		$this->hasColumn("language","string",40);
		$this->hasColumn("relation","string",null);
		$this->hasColumn("coverage","string",null);
		$this->hasColumn("rights","string",null);
		$this->hasColumn("description","string", null);
		$this->hasColumn("source","string",null);
		$this->hasColumn("subject","string",400);
		$this->hasColumn("creator","string",400);
		$this->hasColumn("additional_creator","string",400);
		$this->hasColumn("date","date");
		$this->hasColumn("added","timestamp");
		$this->hasColumn("modified","timestamp");
		$this->hasColumn("item_id","integer");	 
		$this->hasColumn("transcriber","string",null);
		$this->hasColumn("producer","string",null);
		$this->hasColumn("render_device","string",null);
		$this->hasColumn("render_details","string",null);
		$this->hasColumn("capture_date", "timestamp");
		$this->hasColumn("capture_device","string",null);
		$this->hasColumn("capture_details", "string",null);
		$this->hasColumn("change_history","string",null);
		$this->hasColumn("watermark","string",null);
		$this->hasColumn("authentication","string",null);
		$this->hasColumn("encryption", "string",null);
		$this->hasColumn("compression", "string",null);
		$this->hasColumn("post_processing","string",null);
		$this->hasColumn("archive_filename","string",400);
		$this->hasColumn("fullsize_filename","string",400);
		$this->hasColumn("original_filename","string",400);
		$this->hasColumn("thumbnail_filename","string",400);
		$this->hasColumn("size","integer");
		$this->hasColumn("mime_browser","string",400);
		$this->hasColumn("mime_php","string",400);
		$this->hasColumn("mime_os","string",400);
		$this->hasColumn("type_os","string",400);
		$this->hasColumn("modified","timestamp",400);
		$this->hasColumn("added","timestamp","null");
    }
}  	 

?>