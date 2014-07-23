<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2014 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Quasi-form for adding CSRF token checking to manually-created forms.
 *
 * This version uses a per-session token.
 *
 * @version 2.2.2
 * @package Omeka\Form
 */
class Omeka_Form_SessionCsrf extends Omeka_Form
{
    public function init()
    {
        parent::init();
        $this->addElement('sessionCsrfToken', 'csrf_token');
    }

    public function loadDefaultDecorators()
    {
        $this->setDecorators(array('FormElements'));
    }
}
