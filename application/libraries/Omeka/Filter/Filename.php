<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Rename a file to make it suitable for inclusion in the Omeka archive.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 */
class Omeka_Filter_Filename implements Zend_Filter_Interface
{
    /**
     * Grab the original path to the file, rename it according to our 
     * convention, and return the new path to the file.
     * 
     * @param string $value Path to the file.
     * @return string Path to the (renamed) file.
     */
    public function filter($value)
    {
        $filename = basename($value);
        $directory = dirname($value);
        $newFilename = $this->renameFileForArchive($filename);
        
        $targetPath = $directory . '/' . $newFilename;
        $result = rename($value, $targetPath);
        
        return $targetPath;
    }
    
    /**
     * Strip out whitespace, non-printable characters, extra . characters, and 
     * convert all spaces to dashes.  This is applied to every file that is uploaded
     * to Omeka so that there will be no problems with funky characters in filenames.  
     * 
     * @deprecated Filenames are now MD5 hashes, this method should not be used.
     * @param string $name
     * @return string
     */
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
     * Creates a new, random filename for storage in the archive.
     * 
     * @param string $name
     * @return string
     */
    public function renameFileForArchive($name) 
    {
        $extension = strrchr($name, '.');
        $basename = md5(mt_rand() + microtime(true));        
        // Assume that extensions that do not exclusively contain alphanumeric, 
        // hyphen, and underscore characters are invalid, and remove them.
        if (preg_match('/[^a-z0-9_\-]/i', substr($extension, 1))) {
            return $basename;
        }
        return $basename . $extension;
    }
}
