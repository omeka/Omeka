<?php 
require_once 'ItemAtom.php';
$atom = new ItemAtom($items);
echo $atom->getFeed();
?>
