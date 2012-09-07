<?php
class Omeka_View_Helper_Singularize extends Zend_View_Helper_Abstract
{
    public function singularize($var)
    {
        return Inflector::singularize(Inflector::underscore($var));
    }
}
