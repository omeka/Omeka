<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class FilesImages extends Omeka_Record
{    
    public $width;
    public $height;
    public $bit_depth;
    public $channels;
    public $exif_string;
    public $exif_array;
    public $iptc_string;
    public $iptc_array;
    public $file_id;

    public function generate($id3, $path)
    {        
        $size = getimagesize($path, $info);

        $this->width     = $size[0];
        $this->height    = $size[1];
        $this->bit_depth = $size['bits'];
        $this->channels  = $size['channels'];

        //EXIF
        if ($exif = @exif_read_data($path)) {
            $this->exif_array = $exif;
        } else {
            $this->exif_array = array();
        }
        
        if ($iptc = iptcparse($info["APP13"])) {
            $this->iptc_array = $iptc;
        } else {
            $this->iptc_array = array();
        }
        
        //Convert the exif to a string as for to store it
        $exif_string = '';
            foreach ($this->exif_array as $k => $v) {
                $exif_string .= $k . ':';
                if (is_array($v)) {
                    $exif_string .= "\n";
                    foreach ($v as $key => $value) {
                        $exif_string .= "\t" . $key . ':' . $value . "\n";
                    }
                } else {
                    $exif_string .= $v;
                }
                $exif_string .= "\n";
            }
        $this->exif_string = $exif_string;
    }
    
    protected function beforeSave()
    {
        if (is_array($this->exif_array)) {
            $this->exif_array = serialize($this->exif_array);
        }
        
        if (is_array($this->iptc_array)) {
            $this->iptc_array = serialize($this->iptc_array);
        }
    }
}