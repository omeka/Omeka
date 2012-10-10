<?php
require_once realpath(dirname(__FILE__) . '/../bootstrap.php');

// This mini app is in the install/ directory.
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__)));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

