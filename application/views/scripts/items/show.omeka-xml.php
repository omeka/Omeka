<?php
$omekaXml = new Omeka_Output_Xml_Item($item, 'item');
echo $omekaXml->getDoc()->saveXML();
