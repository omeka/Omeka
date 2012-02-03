<?php
echo Omeka_Output_Json::toJson(new Omeka_Output_Xml_Collection($collection, 'collection'));
