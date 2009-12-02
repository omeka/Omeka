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
        $fullPath = rtrim($dirToIm, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR 
                  . Omeka_File_Derivative_Image::IMAGEMAGICK_COMMAND;
        
        // Attempt to run the ImageMagick binary (no arguments).
        exec($fullPath, $output, $returnCode);
        
        // A return value of 0 indicates the binary is working correctly.
        return !(int)$returnCode;
    }
    
    private function _setForm()
    {
        // http://framework.zend.com/manual/en/zend.form.quickstart.html
        // http://devzone.zend.com/article/3450
        $form = new Omeka_Form;
        $form->setMethod('post');
        $form->setAttrib('id', 'settings-form');
        
        $form->addElement('text', 'site_title', array(
            'label' => 'Site Title'
        ));
        
        $form->addElement('textarea', 'description', array(
            'label' => 'Site Description',
        ));
        
        $form->addElement('text', 'administrator_email', array(
            'label' => 'Administrator Email',
            'validators' => array('EmailAddress'), 
            'required' => true
        ));
        
        $form->addElement('text', 'copyright', array(
            'label' => 'Site Copyright Information'
        ));
        
        $form->addElement('text', 'author', array(
            'label' => 'Site Author Information'
        ));
        
        $form->addElement('text', 'fullsize_constraint', array(
            'label' => 'Fullsize Image Size',
            'description' => 'Maximum fullsize image size constraint (in pixels).', 
            'validators' => array('Digits'), 
            'required' => true
        ));
        
        $form->addElement('text', 'thumbnail_constraint', array(
            'label' => 'Thumbnail Size',
            'description' => 'Maximum thumbnail size constraint (in pixels).', 
            'validators' => array('Digits'), 
            'required' => true
        ));
        
        $form->addElement('text', 'square_thumbnail_constraint', array(
            'label' => 'Square Thumbnail Size', 
            'description' => 'Maximum square thumbnail size constraint (in pixels).', 
            'validators' => array('Digits'), 
            'required' => true
        ));
        
        $form->addElement('text', 'per_page_admin', array(
            'label' => 'Items Per Page (admin)', 
            'description' => 'Limit the number of items displayed per page in the administrative interface.', 
            'validators' => array('Digits'), 
            'required' => true
        ));
        
        $form->addElement('text', 'per_page_public', array(
            'label' => 'Items Per Page (public)', 
            'description' => 'Limit the number of items displayed per page in the public interface.', 
            'validators' => array('Digits'), 
            'required' => true
        ));
        
        $form->addElement('checkbox', 'show_empty_elements', array(
            'label' => 'Show Empty Elements',
            'class' => 'checkbox'
        ));

        $form->addElement('text', 'path_to_convert', array(
            'label' => 'Imagemagick Directory Path'
        ));
        
        $form->addElement('submit', 'settings_submit', array(
            'label' => 'Save Settings'
        ));
        
        $form->addDisplayGroup(
            array('administrator_email', 'site_title', 'description', 
                  'copyright', 'author', 'fullsize_constraint', 
                  'thumbnail_constraint', 'square_thumbnail_constraint', 
                  'per_page_admin', 'per_page_public', 'show_empty_elements', 'path_to_convert'), 
            'site_settings');
        
        $form->addDisplayGroup(
            array('settings_submit'), 
            'submit');
        
        $form->setDefaults(Omeka_Context::getInstance()->getOptions());
        
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
