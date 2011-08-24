<?php
$omekaXml = new Omeka_Output_Xml_ItemContainer($items, 'itemContainer');
echo $omekaXml->getDoc()->saveXML();
