<?php
$omekaXml = new ItemOmekaXml($item, 'item');
echo $omekaXml->getDoc()->saveXML();