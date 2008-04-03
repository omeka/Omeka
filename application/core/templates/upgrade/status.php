<?php head(); ?>
<h2 class="instruction">Omeka is now upgrading itself.  Please refresh your screen once this page finishes loading.</h2>

<?php if(!$success): ?>
    <p class="error">Omeka encountered an error when upgrading your installation.  The full text of this error has been emailed to your administrator:</p>
	
	<?php foreach( $errors as $num => $error ): ?>
	   <p class="error_text"><?php echo htmlentities($error); ?></p>
	<?php endforeach; ?>

<?php endif; ?>	

<?php foreach( $output as $num => $text ): ?>
    <p class="success"><?php echo htmlentities($text); ?></p>
<?php endforeach; ?>    

<?php foot(); ?>