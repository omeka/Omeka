<?php
$omekaXml = new ItemOmekaXml($item);
echo $omekaXml->getDoc()->saveXML();