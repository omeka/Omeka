<?php
$omekaXml = new Output_FileOmekaXml($file, 'file');
echo $omekaXml->getDoc()->saveXML();
