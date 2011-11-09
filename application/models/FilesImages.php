<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 */
class FilesImages
{
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
        if(isset($info['APP13'])) {
            $this->iptc = $this->get_iptc_info($info["APP13"]);
        }
        
    }

    private function get_iptc_info($info)
    {
        $iptc_array = array();
        if (function_exists('iptcparse')) {
            $iptc_match = array();
            $iptc_match['2#120'] = "caption";
            $iptc_match['2#122'] = "caption_writer";
            $iptc_match['2#105'] = "headline";
            $iptc_match['2#040'] = "special_instructions";
            $iptc_match['2#080'] = "byline";
            $iptc_match['2#085'] = "byline_title";
            $iptc_match['2#110'] = "credit";
            $iptc_match['2#115'] = "source";
            $iptc_match['2#005'] = "object_name";
            $iptc_match['2#055'] = "date_created";
            $iptc_match['2#090'] = "city";
            $iptc_match['2#095'] = "state";
            $iptc_match['2#101'] = "country";
            $iptc_match['2#103'] = "original_transmission_reference";
            $iptc_match['2#015'] = "category";
            $iptc_match['2#020'] = "supplemental_category";
            $iptc_match['2#025'] = "keyword";
            $iptc_match['2#116'] = "copyright_notice";

            $iptc = iptcparse($info);
            if (is_array($iptc)) {
                foreach ($iptc as $key => $val) {
                    if (isset($iptc_match[$key])) {
                        $iptc_info = "";
                        foreach ($val as $v) {
                            $iptc_info .= (($iptc_info != "" ) ? ", " : "").$v;
                        }
                        if ($key == "2#055") {
                        $iptc_array[$iptc_match[$key]] = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\3.\\2.\\1", $iptc_info);
                        } else {
                            $iptc_array[$iptc_match[$key]] = $iptc_info;
                        }
                    }
                }
            }
        }
        return $iptc_array;
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
        if(isset($this->size['channels'])) {
            return $this->size['channels'];
        }
        
    }

    public function getExifArray()
    {
        if (!empty($this->exif)) {
            return serialize($this->exif);
        }
    }

    public function getExifString()
    {
        if (!empty($this->exif)) {
            return $this->getEmbeddedMetadataString($this->exif);
        }
    }

    public function getIPTCArray()
    {
        if(!empty($this->iptc)) {
            return serialize($this->iptc);
        }
    }

    public function getIPTCString()
    {
        if(!empty($this->iptc)) {
            return $this->getEmbeddedMetadataString($this->iptc);
        }
    }
    
    private function getEmbeddedMetadataString($metadata = array())
    {
        //Convert the metadata to a string as for to store it
        $metadata_string = '';
            foreach ($metadata as $k => $v) {
                $metadata_string .= $k . ':';
                if (is_array($v)) {
                    $metadata_string .= "\n";
                    foreach ($v as $key => $value) {
                        $metadata_string .= "\t" . $key . ':' . $value . "\n";
                    }
                } else {
                    $metadata_string .= $v;
                }
                $metadata_string .= "\n";
            }
        return $metadata_string;
    }
}
