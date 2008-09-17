<?php
require_once 'ElementSet.php';
/**
* 
*/
class ElementSetsController extends Omeka_Controller_Action
{
    public function init()
    {
        $this->_modelClass = 'ElementSet';
    }
}
