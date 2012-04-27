<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2012
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

 
/**
 * View helper to display messages from FlashMessenger.
 *
 * @package Omeka
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
     * @param string|null $msgStatus Status of messages to retrieve.
     * @return string HTML for messages.
     */
    public function flash($msgStatus = null)
    {
        $flashHtml = '';
        if ($this->_flashMessenger->hasMessages($msgStatus)
         || $this->_flashMessenger->hasCurrentMessages($msgStatus)) {
            $flashHtml .= '<div id="flash">' . "\n" . '<ul>';
            if ($msgStatus) {
                foreach ($this->_flashMessenger->getMessages($msgStatus) as $message) {
                    $flashHtml .= $this->_getListHtml($msgStatus, $message);
                }
                foreach ($this->_flashMessenger->getCurrentMessages($msgStatus) as $message) {
                    $flashHtml .= $this->_getListHtml($msgStatus, $message);
                }
            } else {
                foreach ($this->_flashMessenger->getMessages($msgStatus) as $status => $messages) {
                    foreach ($messages as $message) {
                        $flashHtml .= $this->_getListHtml($status, $message);
                    }
                }
                foreach ($this->_flashMessenger->getCurrentMessages($msgStatus) as $status => $messages) {
                    foreach ($messages as $message) {
                        $flashHtml .= $this->_getListHtml($status, $message);
                    }
                }
            }
            $flashHtml .= '</ul></div>';
        }
        $this->_flashMessenger->clearMessages($msgStatus);
        $this->_flashMessenger->clearCurrentMessages($msgStatus);
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
            . $this->view->escape($message)
            . '</li>';
    }
}
