<?php
$omekaXml = new FileOmekaXml($file, 'file');
echo $omekaXml->getDoc()->saveXML();