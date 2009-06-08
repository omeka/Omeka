<?php
require_once 'bootstrap.php';

// Initialize the class loader (better way to do this?)
require_once 'Omeka/Core.php';
$core = new Omeka_Core;
$core->initializeClassLoader();