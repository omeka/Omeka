<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Rename a file to make it suitable for inclusion in the Omeka archive.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Filter_Filename implements Zend_Filter_Interface
{
    /**
     * Grab the original path to the file, rename it according to our 
     * convention, and return the new path to the file.
     * 
     * @param string Path to the file.
     * @return string Path to the (renamed) file.
     **/
    public function filter($value)
    {
        $filename = basename($value);
        $directory = dirname($value);
        $newFilename = $this->renameFileForArchive($filename);
        
        $targetPath = $directory . DIRECTORY_SEPARATOR . $newFilename;
        $result = rename($value, $targetPath);
        
        return $targetPath;
    }
    
    /**
     * Strip out whitespace, non-printable characters, extra . characters, and 
     * convert all spaces to dashes.  This is applied to every file that is uploaded
     * to Omeka so that there will be no problems with funky characters in filenames.  
     * 
     * @todo It may be easier just to generate a long string of random numbers 
     * and characters for each new file, rather than actually trying to maintain 
     * the old filename, which is still stored in the database.  This would only 
     * be an issue if the archives directory needs to be human-readable, and there 
     * is no guarantee that it does.
     * 
     * @param string
     * @return string
     **/
    public function sanitizeFilename($name)
    {
        //Strip whitespace
        $name = trim($name);
        
        /*    Remove all but last .
            I wish there was an easier way of doing this */
        if(substr_count($name,'.') > 1) {
            $array = explode('.',$name);
            if(count($array) > 2) {
                $last = array_pop($array);
                $first = join('', $array);
                $name = array();
                if(!empty($first)) {
                    $name = $first;
                }
                if(!empty($last)) {
                    $name .= '.'.$last;
                }
            }
        }
        
        //Strip out invalid characters
        $invalid = array('"','*','/',':','<','>','?','|',"'",'&',';','#','\\');
        $name = str_replace($invalid, '', $name);
        
        //Strip out non-printable characters
        for ($i = 0; $i < 32; $i++) { 
            $nonPrintable[$i] = chr($i);
        }
        $name = str_replace($nonPrintable, '', $name);
        
        //Convert to lowercase (avoid corrupting UTF-8)
        $name = strtolower($name);
        
        //Convert remaining spaces to hyphens
        $name = str_replace(' ', '-', $name);
        
        return $name;
    }
    
    /**
     * Sanitize the filename and append a set of random characters to the end of
     *  the filename but before the suffix.
     * 
     * @param string
     * @return string
     **/
    public function renameFileForArchive($name) {
        
        $name = $this->sanitizeFilename($name);
        
        $new_name     = explode('.', $name);
        $new_name[0] .= '_' . substr(md5(mt_rand() + microtime(true)), 0, 10);
        $new_name_string = implode('.', $new_name);
        
        return $new_name_string;
    }
}
