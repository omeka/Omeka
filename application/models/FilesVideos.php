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

	protected $id3;

	public function generate($info, $path)
	{		
		$this->id3 = $info;
		
		$stream = array();
		if(array_key_exists('video', $info)) {
			
			if(array_key_exists('streams', $info['video'])) {
				$stream = array_pop($info['video']['streams']);
			}else {
				$stream = $info['video'];
			}
		}
				
		$this->bitrate = (int) $info['bitrate'];
		$this->duration = (int) $info['playtime_seconds'];
		$this->codec = $stream['codec'];
		$this->width = (int) $stream['resolution_x'];
		$this->height = (int) $stream['resolution_y'];
		$this->sample_rate = $this->getSampleRate();
	}
	
	protected function getSampleRate()
	{
		if(array_key_exists('quicktime', $this->id3)) {
			//Screw quicktime (no consistencies in data format)
			return '0';
			//return (int) $this->id3['quicktime']['audio']['sample_rate'];
		}
		else {
			return (int) $this->id3['audio']['sample_rate'];
		}
	}	
}

?>