<?php
$omekaXml = new Omeka_Output_Xml_Collection($collection, 'collection');
echo $omekaXml->getDoc()->saveXML();
