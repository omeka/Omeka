<?php
if (PHP_VERSION_ID >= 70200) {
    require_once __DIR__ . '/_classes/TestCase72.php';
} else if (PHP_VERSION_ID >= 70100) {
    require_once __DIR__ . '/_classes/TestCase71.php';
} else {
    require_once __DIR__ . '/_classes/TestCase70.php';
}
