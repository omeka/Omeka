<?php
    $path = getcwd()."/";  
	$upgradepath = $path.'upgrade.php';
echo "<h2>Omeka Upgrade</h2>";
echo "<p>We've added the ability to create square thumbnails in this new version of Omeka.  You <u>must</u> do the following to continue using Omeka:</p>";
echo "<ol><li>change the permissions of the square_thumbnails directory (located at /archive/square_thumbnails/) to 777</li>";
echo "<li>enter in the maximum square thumbnail constraint in the form, and submit it.  (the default value is 100, you can change this later in the admin panel if you wish to do so)<br /><form method='POST'><strong>max square thumbnail constraint (in px):</strong>  <input type='text' name='square_thumbnail_constraint' value='100'><input type='submit' name='submit' value='submit'></form></li></ol>";

?>