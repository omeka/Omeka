<?php
/**
 * FilesVideo
 * @package: Omeka
 */
class FilesVideos extends Omeka_Record
{	
	public $bitrate;
	public $duration;
	public $codec;
	public $sample_rate;
	public $width;
	public $height;
	public $file_id;

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