<?php head(); ?>
<h2 class="instruction">Omeka is now upgrading itself.  Please refresh your screen once this page finishes loading.</h2>

<?php if(!$success): ?>
    <p class="error">Omeka encountered an error when upgrading your installation:</p>
	
	<?php foreach( $errors as $num => $error ): ?>
	   <p class="error_text"><?php echo htmlentities($error); ?></p>
	<?php endforeach; ?>

<?php endif; ?>	

<?php foreach( $output as $num => $text ): ?>
    <div class="success">
    <?php foreach( $text as $out ): ?>
        <p><?php echo htmlentities($out); ?></p>
    <?php endforeach; ?>
    </div>
<?php endforeach; ?>    

<?php foot(); ?>