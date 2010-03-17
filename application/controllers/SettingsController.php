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
    const DEFAULT_FULLSIZE_CONSTRAINT = 800;
    const DEFAULT_THUMBNAIL_CONSTRAINT = 200;
    const DEFAULT_SQUARE_THUMBNAIL_CONSTRAINT = 200;
    const DEFAULT_PER_PAGE_ADMIN = 10;
    const DEFAULT_PER_PAGE_PUBLIC = 10;
    
    private $_form;
    
    public function indexAction() 
    {
        $this->_forward('edit');
    }
    
    public function browseAction() 
    {
        $this->_forward('edit');
    }
    
    public function editAction() 
    {
        $this->_setForm();
        $this->view->form = $this->_form;
        
        if (isset($_POST['settings_submit'])) {
            if ($this->_form->isValid($_POST)) {
                $this->_setOptions();
                $this->flashSuccess('The general settings have been updated.');
            } else {
                $this->flashError('There were errors found in your form. Please edit and resubmit.');
            }
        }
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
        $filePath = rtrim($dirToIm, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR 
                  . Omeka_File_Derivative_Image::IMAGEMAGICK_COMMAND;
        
        //Make sure the file is executable
        if (!is_executable($filePath)) {
            return false;
        }
                        
        // Attempt to run the ImageMagick binary with the version argument
        // If you try to run it without any arguments, it returns an error code
        $fullPath = $filePath . ' -version';
        exec($fullPath, $output, $returnCode);        
                
        // A return value of 0 indicates the binary is working correctly.
        return !(int)$returnCode;
    }
    
    private function _setForm()
    {
        require_once APP_DIR . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'GeneralSettings.php';
        $form = new Omeka_Form_GeneralSettings;
        $form->setDefaults($this->getInvokeArg('bootstrap')->getResource('Options'));
        $this->_form = $form;
    }
    
    private function _setOptions()
    {
        // Insert the form options to the options table.
        $options = array('administrator_email', 
                         'copyright', 
                         'site_title', 
                         'author', 
                         'description', 
                         'thumbnail_constraint', 
                         'square_thumbnail_constraint', 
                         'fullsize_constraint', 
                         'per_page_admin', 
                         'per_page_public',
                         'show_empty_elements',
                         'path_to_convert');
        foreach ($options as $option) {
            set_option($option, $this->_form->getValue($option));
        }
    }
}
