<?php 
//Use the classes that have already been built for converting records to XML
require_once 'ItemXml.php';
$converter = new ItemXml(); 
echo $converter->renderAll($items); ?>