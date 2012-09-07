<?php
class Omeka_View_Helper_Pluralize extends Zend_View_Helper_Abstract
{
    public function pluralize($var)
    {
        return Inflector::tableize($var);
    }
}
