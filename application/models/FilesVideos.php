<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class FilesVideos
{        
    public function initialize($id3, $pathToFile)
    {
        $this->info = $id3;
        $this->pathToFile = $pathToFile;

        $stream = array();
        if (array_key_exists('video', $id3)) {
            
            if (array_key_exists('streams', $id3['video'])) {
                $stream = array_pop($id3['video']['streams']);
            } else {
                $stream = $id3['video'];
            }
        }
        
        $this->stream = $stream;
    }
        
    public function getBitrate()
    {
        return (int) $this->info['bitrate'];
    }
    
    public function getDuration()
    {
        return (int) $this->info['playtime_seconds'];
    }
    
    public function getCodec()
    {
        return $this->stream['codec'];
    }
    
    public function getWidth()
    {
        return (int) $this->stream['resolution_x'];
    }
    
    public function getHeight()
    {
        return (int) $this->stream['resolution_y'];
    }

    public function getSampleRate()
    {
        if (array_key_exists('quicktime', $this->info)) {
            // Screw quicktime (no consistencies in data format)
            return '0';
            // return (int) $this->id3['quicktime']['audio']['sample_rate'];
        } else {
            return (int) $this->info['audio']['sample_rate'];
        }
    }    
}