<?php
/**
 * FilesImages
 * @package: Omeka
 */
class FilesImages extends Omeka_Record
{	
    public function setTableDefinition()
    {
		$this->hasColumn('width', 'integer');
		$this->hasColumn('height', 'integer');
		$this->hasColumn('bit_depth', 'integer');
		$this->hasColumn('channels', 'integer');
		$this->hasColumn('exif_string', 'string');
		$this->hasColumn('exif_array', 'array');
		$this->hasColumn('iptc_string', 'string');
		$this->hasColumn('iptc_array', 'array');
		$this->hasColumn('file_id', 'integer', null, array('range'=>array('1')));
    }
    public function setUp()
    {
		$this->hasOne('File', 'FilesImages.file_id');
    }

	public function generate($id3, $path)
	{
//		$path = FILES_DIR.DIRECTORY_SEPARATOR.$this->File->archive_filename;
		
		$size = getimagesize($path, $info);

		$this->width = $size[0];
		$this->height = $size[1];
		$this->bit_depth = $size['bits'];
		$this->channels = $size['channels'];

		//EXIF
		if($exif = @exif_read_data($path)) {
			$this->exif_array = $exif;
		}else {
			$this->exif_array = array();
		}
		
		if($iptc = iptcparse($info["APP13"])) {
			$this->iptc_array = $iptc;
		}else {
			$this->iptc_array = array();
		}
		
		//Convert the exif to a string as for to store it
		$exif_string = '';
			foreach ($exif as $k => $v) {
				$exif_string .= $k . ':';
				if(is_array($v)) {
					$exif_string .= "\n";
					foreach ($v as $key => $value) {
						$exif_string .= "\t" . $key . ':' . $value . "\n";
					}
				}else {
					$exif_string .= $v;
				}
				$exif_string .= "\n";
			}	
		
		$this->exif_string = $exif_string;
		
	}
}

?>