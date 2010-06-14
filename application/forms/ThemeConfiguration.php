<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Forms
 **/

/**
 * Configuration form for theme options
 *
 * @package Omeka
 * @subpackage Forms
 * @copyright Center for History and New Media, 2010
 **/
class Omeka_Form_ThemeConfiguration extends Omeka_Form
{
    const THEME_FILE_HIDDEN_FIELD_NAME_PREFIX = 'hidden_file_';
    
    protected $_themeName;
    
    public function init()
    {
        parent::init();
        $themeName = $this->getThemeName();
        
        $theme = Theme::getAvailable($themeName);
        $themeConfigIni = $theme->path . DIRECTORY_SEPARATOR . 'config.ini';
        $themeOptionName = Theme::getOptionName($themeName);

        if (file_exists($themeConfigIni) && is_readable($themeConfigIni)) {

            // get the theme configuration form specification
            $formElementsIni = new Zend_Config_Ini($themeConfigIni, 'config');
            $configIni = new Zend_Config(array('elements' => $formElementsIni));

            // create an omeka form from the configuration file
            $this->setConfig($configIni);
            $this->setAction("");
            $this->setAttrib('enctype', 'multipart/form-data');

            // add the 'Save Changes' submit button                      
            $this->addElement(
                'submit', 
                'submit', 
                array(
                    'label' => 'Save Changes'
                )
            );

            // configure all of the form elements
            $elements = $this->getElements();
            $newElements = array();
            foreach($elements as $element) {
                if ($element instanceof Zend_Form_Element_File) {

                    // set all the file elements destination directories
                    $element->setDestination(THEME_UPLOADS_DIR);
                    $fileName = get_theme_option($element->getName(), $themeName);                    

                    // add a hidden field to store whether already exists
                    $hiddenElement = new Zend_Form_Element_Hidden(self::THEME_FILE_HIDDEN_FIELD_NAME_PREFIX . $element->getName());
                    $hiddenElement->setValue($fileName);
                    $hiddenElement->setDecorators(array('ViewHelper', 'Errors'));
                    $newElements[] = $hiddenElement;

                }
                $newElements[] = $element;
            }
            $this->setElements($newElements);            

            // set all of the form element values
            $themeConfigValues = Theme::getOptions($themeName);
            foreach($themeConfigValues as $key => $value) {
                if ($this->getElement($key)) {
                    $this->$key->setValue($value);
                }
            }
        }
    }
    
    public function setThemeName($themeName)
    {
        $this->_themeName = $themeName;
    }
    
    public function getThemeName()
    {
        return $this->_themeName;
    }
}