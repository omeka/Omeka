<?php
/**
 * FilesVideo
 * @package: Omeka
 */
class FilesVideos extends Omeka_Record
{	
    public function setTableDefinition()
    {
		$this->hasColumn('bitrate', 'integer', null, array('unsigned'=>true));
		$this->hasColumn('duration', 'integer', null, array('unsigned'=>true));
		$this->hasColumn('codec', 'string');
		$this->hasColumn('sample_rate', 'integer', null, array('unsigned'=>true));
		$this->hasColumn('width', 'integer', null, array('unsigned'=>true));
		$this->hasColumn('height', 'integer', null, array('unsigned'=>true));
		$this->hasColumn('file_id', 'integer', null, array('unsigned'=>true, 'range'=>array('1'), 'notnull'=>true));
    }
    public function setUp()
    {
		$this->hasOne('File', 'FilesVideos.file_id');
    }

	public function generate($info, $path)
	{		
		$streamInfo = array_pop($info['video']['streams']);
		
		$this->bitrate = (int) $info['bitrate'];
		$this->duration = (int) $info['playtime_seconds'];
		$this->codec = $streamInfo['codec'];
		$this->width = (int) $streamInfo['resolution_x'];
		$this->height = (int) $streamInfo['resolution_y'];
		$this->sample_rate = (int) $info['audio']['sample_rate'];
	}
}

?>