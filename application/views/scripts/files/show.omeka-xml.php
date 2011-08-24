<?php
$omekaXml = new Omeka_Output_Xml_File($file, 'file');
echo $omekaXml->getDoc()->saveXML();
