<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Define custom behavior for the default whitelist file extension validator.
 * 
 * Baseline behavior of this class is to tweak the default error messages.  
 * Messages are intentionally as detailed as possible.  Note that it is the 
 * responsibility of plugin writers to suppress or replace these messages if 
 * necessary for security reasons, e.g. if displaying it to the end user might 
 * expose the site to vulnerability probes.
 * 
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 */
class Omeka_Validate_File_Extension extends Zend_Validate_File_Extension
{
    const DEFAULT_WHITELIST = 'aac,aif,aiff,asf,asx,avi,bmp,c,cc,class,css,divx,doc,docx,exe,gif,gz,gzip,h,ico,j2k,jp2,jpe,jpeg,jpg,m4a,mdb,mid,midi,mov,mp2,mp3,mp4,mpa,mpe,mpeg,mpg,mpp,odb,odc,odf,odg,odp,ods,odt,ogg, pdf,png,pot,pps,ppt,pptx,qt,ra,ram,rtf,rtx,swf,tar,tif,tiff,txt, wav,wax,wma,wmv,wmx,wri,xla,xls,xlsx,xlt,xlw,zip';
    const WHITELIST_OPTION = 'file_extension_whitelist';
    
    /**
     * Overrides default error message templates.
     * @var array
     */
    protected $_messageTemplates = array(
        self::FALSE_EXTENSION => "The file '%value%' could not be ingested because it has a disallowed file extension (%target_extension%).",
        self::NOT_FOUND       => "The file '%value%' is missing and could not be ingested."
    );
    
    /**
     * The extension of the file being validated
     * @var string
     */
    protected $_targetExtension;
    
    /**
     * Constructor retrieves the whitelist from the database if no arguments are
     * given.
     * 
     * @param mixed $options
     * @return void
     */
    public function __construct($options = null)
    {
        if (!$options) {
            $options = (string)get_option(self::WHITELIST_OPTION);
        }
        parent::__construct($options);
        $this->_messageVariables['target_extension'] = '_targetExtension';
    }
    
    
    /**
     * Returns true if and only if the fileextension of $value is included in 
     * the set extension list.
     *
     * @param string $value Real file to check for extension.
     * @param array $file File data from Zend_File_Transfer.
     * @return boolean
     */
    public function isValid($value, $file = null)
    {                
        // Is file readable ?
        require_once 'Zend/Loader.php';
        if (!Zend_Loader::isReadable($value)) {
            return $this->_throw($file, self::NOT_FOUND);
        }

        if ($file !== null) {
            $info['extension'] = substr($file['name'], strrpos($file['name'], '.') + 1);
        } else {
            $info = pathinfo($value);
        }
        
        //set the target extension
        $this->_targetExtension = $info['extension'];
        
        return parent::isValid($value, $file);
    }
}
