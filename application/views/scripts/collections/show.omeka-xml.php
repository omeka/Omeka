<?php
$omekaXml = new Output_CollectionOmekaXml($collection, 'collection');
echo $omekaXml->getDoc()->saveXML();
