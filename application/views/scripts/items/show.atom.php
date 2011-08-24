<?php
require_once 'ItemAtom.php';
$atom = new ItemAtom(array($item));
echo $atom->getFeed();
?>
