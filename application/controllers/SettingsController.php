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
                $this->flashSuccess('The settings have been updated.');
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
        $form = new Zend_Form;
        $form->setMethod('post');
        $form->setAttrib('id', 'settings-form');
        $form->removeDecorator('HtmlTag');
        
        // Add form elements.
        $elementDecorators = array('ViewHelper', 
                                   'Errors', 
                                   'Description',
                                   'Label', 
                                   array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'field')));
        
        $form->addElement('text', 'site_title', array(
            'label' => 'Site Title', 
            'class' => 'textinput',
            'value' => get_option('site_title'), 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('textarea', 'description', array(
            'label' => 'Site Description', 
            'class' => 'textinput',
            'value' => get_option('description'), 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('text', 'administrator_email', array(
            'label' => 'Administrator Email',
            'class' => 'textinput',
            
            'value' => get_option('administrator_email'), 
            'validators' => array('EmailAddress'), 
            'required' => true, 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('text', 'copyright', array(
            'label' => 'Site Copyright Information',
            'class' => 'textinput',
            'value' => get_option('copyright'), 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('text', 'author', array(
            'label' => 'Site Author Information',
            'class' => 'textinput', 
            'value' => get_option('author'), 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('text', 'fullsize_constraint', array(
            'label' => 'Fullsize Image Size',
            'class' => 'textinput',
            'description' => 'Maximum fullsize image size constraint (in pixels).', 
            'value' => get_option('fullsize_constraint'), 
            'validators' => array('Digits'), 
            'required' => true, 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('text', 'thumbnail_constraint', array(
            'label' => 'Thumbnail Size',
            'class' => 'textinput',
            'description' => 'Maximum thumbnail size constraint (in pixels).', 
            'value' => get_option('thumbnail_constraint'), 
            'validators' => array('Digits'), 
            'required' => true, 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('text', 'square_thumbnail_constraint', array(
            'label' => 'Square Thumbnail Size', 
            'class' => 'textinput',
            'description' => 'Maximum square thumbnail size constraint (in pixels).', 
            'value' => get_option('square_thumbnail_constraint'), 
            'validators' => array('Digits'), 
            'required' => true, 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('text', 'per_page_admin', array(
            'label' => 'Items Per Page (admin)', 
            'class' => 'textinput',
            'description' => 'Limit the number of items displayed per page in the administrative interface.', 
            'value' => get_option('per_page_admin'), 
            'validators' => array('Digits'), 
            'required' => true, 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('text', 'per_page_public', array(
            'label' => 'Items Per Page (public)', 
            'class' => 'textinput',
            'description' => 'Limit the number of items displayed per page in the public interface.', 
            'value' => get_option('per_page_public'), 
            'validators' => array('Digits'), 
            'required' => true, 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('checkbox', 'show_empty_elements', array(
            'label' => 'Show Empty Elements',
            'class' => 'checkbox',
            'value' => get_option('show_empty_elements'),
            'decorators' => $elementDecorators
        ));

        $form->addElement('text', 'path_to_convert', array(
            'label' => 'Imagemagick Directory Path', 
            'class' => 'textinput',
            'value' => get_option('path_to_convert'), 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('submit', 'settings_submit', array(
            'label' => 'Save Settings', 
            'class' => 'submit',
            'decorators' => array('Tooltip', 'ViewHelper')
        ));

        // Add fieldsets.
        $displayGroupDecorators = array('FormElements', 'Fieldset');
        
        $form->addDisplayGroup(
            array('administrator_email', 'site_title', 'description', 
                  'copyright', 'author', 'fullsize_constraint', 
                  'thumbnail_constraint', 'square_thumbnail_constraint', 
                  'per_page_admin', 'per_page_public', 'show_empty_elements', 'path_to_convert'), 
            'site_settings', 
            array('decorators' => $displayGroupDecorators)
        );
        
        $form->addDisplayGroup(
            array('settings_submit'), 
            'submit', 
            array('decorators' => $displayGroupDecorators)
        );
        
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
