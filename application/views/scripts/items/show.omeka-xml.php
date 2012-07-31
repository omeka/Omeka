<?php
$omekaXml = new Output_ItemOmekaXml($item, 'item');
echo $omekaXml->getDoc()->saveXML();
