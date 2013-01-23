<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * View helper to display messages from FlashMessenger.
 * 
 * @package Omeka\View\Helper
 */
class Omeka_View_Helper_Flash extends Zend_View_Helper_Abstract
{
    /**
     * @var Omeka_Controller_Action_Helper_FlashMessenger
     */
    private $_flashMessenger;

    public function __construct()
    {
        $this->_flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
    }

    /**
     * Display messages from the FlashMessenger.
     *
     * @return string HTML for messages.
     */
    public function flash()
    {
        $flashHtml = '';
        if ($this->_flashMessenger->hasMessages()
         || $this->_flashMessenger->hasCurrentMessages()) {
            $flashHtml .= '<div id="flash">' . "\n" . '<ul>';
            foreach ($this->_flashMessenger->getMessages() as $status => $messages) {
                foreach ($messages as $message) {
                    $flashHtml .= $this->_getListHtml($status, $message);
                }
            }
            foreach ($this->_flashMessenger->getCurrentMessages() as $status => $messages) {
                foreach ($messages as $message) {
                    $flashHtml .= $this->_getListHtml($status, $message);
                }
            }
            $flashHtml .= '</ul></div>';
        }
        $this->_flashMessenger->clearMessages();
        $this->_flashMessenger->clearCurrentMessages();
        return $flashHtml;
    }

    /**
     * Get the HTML for a message.
     *
     * @param string $status
     * @param string $message
     * @return string
     */
    private function _getListHtml($status, $message)
    {
        return '<li class="' . $this->view->escape($status) . '">' 
            . nl2br($this->view->escape($message))
            . '</li>';
    }
}
