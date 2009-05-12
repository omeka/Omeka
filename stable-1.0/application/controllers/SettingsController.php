<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @see Omeka_Controller_Action
 */
require_once 'Omeka/Controller/Action.php';

/**
 * @package Omeka
 * @subpackage Controllers
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class SettingsController extends Omeka_Controller_Action
{    
    public function indexAction() {
        $this->_forward('edit');
    }
    
    public function browseAction() {
        $this->_forward('edit');
    }
    
    public function editAction() {
        //Any changes to this list should be reflected in the install script (and possibly the view functions)        
        $settingsList = array('site_title', 
                              'copyright',
                              'administrator_email',
                              'author', 
                              'description', 
                              'thumbnail_constraint', 
                              'square_thumbnail_constraint',
                              'fullsize_constraint', 
                              'per_page_admin', 
                              'per_page_public', 
                              'path_to_convert',
                              'file_extension_whitelist',
                              'file_mime_type_whitelist',
                              'disable_default_file_validation');
        
        $options = Omeka_Context::getInstance()->getOptions();
        
        foreach ($options as $k => $v) {
            if (in_array($k, $settingsList)) {
                $settings[$k] = $v;
            }
        }
        
        $optionTable = $this->getTable('Option')->getTableName();
        $conn = $this->getDb();
        
        //process the form
        if (!empty($_POST)) {
            $sql = "UPDATE $optionTable SET value = ? WHERE name = ?";
            foreach ( $_POST as $key => $value ) {
                if (array_key_exists($key,$settings)) {
                    $conn->exec($sql, array($value, $key));
                    $settings[$key] = $value;
                    $options[$key] = $value;
                }
            }
            Omeka_Context::getInstance()->setOptions($options);
            
            $this->flash("Settings have been changed.");
        }
        
        $this->view->assign($settings);
    }
    
    /**
     * Determine whether or not ImageMagick has been correctly installed and
     * configured.  
     * 
     * In a few cases, this will indicate failure even though the ImageMagick
     * program works properly.  In those cases, users may ignore the results of
     * this test.  This is because the 'convert' command may have returned a 
     * non-zero status code for some reason.  Keep in mind that a 0 status code 
     * always indicates success.
     *
     * @return boolean True if the command line return status is 0 when
     * attempting to run ImageMagick's convert utility, false otherwise.
     **/
    public function checkImagemagickAction()
    {
        $imPath = $this->_getParam('path-to-convert');
        $this->_helper->viewRenderer->setNoRender(true);
        $isValid = $this->_isValidImageMagickPath($imPath);
        $this->getResponse()->setBody($isValid 
                                    ? '<div class="im-success">Works</div>' 
                                    : '<div class="im-failure">Fails</div>');
    }
    
    /**
     * Determine whether or not the path given to ImageMagick is valid.
     * 
     * @param string
     * @return boolean
     **/
    private function _isValidImageMagickPath($dirToIm)
    {
        if (!realpath($dirToIm)) {
            return false;
        }
        if (!is_dir($dirToIm)) {
            return false;
        }
        // Append the binary to the given path.
        $fullPath = rtrim($dirToIm, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR 
                  . Omeka_File_Derivative_Image::IMAGEMAGICK_COMMAND;
        
        // Attempt to run the ImageMagick binary (no arguments).
        exec($fullPath, $output, $returnCode);
        
        // A return value of 0 indicates the binary is working correctly.
        return !(int)$returnCode;
    }
}