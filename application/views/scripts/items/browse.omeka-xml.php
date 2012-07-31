<?php
$omekaXml = new Output_ItemContainerOmekaXml($items, 'itemContainer');
echo $omekaXml->getDoc()->saveXML();
