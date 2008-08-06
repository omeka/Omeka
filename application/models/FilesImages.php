<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @todo Testing.
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class FilesImages
{    
    public $iptc_string;
    public $iptc_array;
    
    public function initialize($id3, $pathToFile)
    {
        $this->id3 = $id3;
        $this->pathToFile = $pathToFile;
        
        $this->size = getimagesize($pathToFile, $info);
        $this->info = $info;
        
        // Extract EXIF data if possible.
        if (function_exists('exif_read_data') and ($exif = @exif_read_data($this->pathToFile))) {
            $this->exif = $exif;
        } else {
            $this->exif = array();
        }
        
        // Extract IPTC data also if possible.
        if (function_exists('iptcparse') and ($iptc = iptcparse($id3["APP13"]))) {
            $this->iptc = $iptc;
        } else {
            $this->iptc = array();
        }
    }
    
    public function getWidth()
    {
        return $this->size[0];
    }
    
    public function getHeight()
    {
        return $this->size[1];
    }
    
    public function getBitDepth()
    {
        return $this->size['bits'];
    }
    
    public function getChannels()
    {
        return $this->size['channels'];
    }
    
    public function getExifArray()
    {
        if (!empty($this->exif)) {
            return serialize($this->exif);
        }
    }
    
    public function getExifString()
    {
        //Convert the exif to a string as for to store it
        $exif_string = '';
            foreach ($this->exif as $k => $v) {
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
        return $exif_string;       
    }
}