<?php
class ApiController extends Omeka_Controller_AbstractActionController
{
    public function indexAction()
    {
        echo '<pre>'; print_r($this->getRequest()->getParams()); echo '</pre>'; exit;
    }
    public function getAction()
    {
        echo '<pre>'; print_r($this->getRequest()->getParams()); echo '</pre>'; exit;
    }
}
