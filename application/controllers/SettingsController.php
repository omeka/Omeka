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
                              'path_to_convert');
        
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
                    $value = get_magic_quotes_gpc() ? stripslashes( $value ) : $value;
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
}