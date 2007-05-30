<?php head(); ?>
<?php common('settings-nav'); ?>

<?php if(!empty($new_names)):?>
<h2>The following plugins have not yet been installed</h2>

<form method="post">

<ol>
<?php foreach( $new_names as $name ): ?>
	<li>
		<input type="checkbox" name="<?php echo $name; ?>" />
		<?php  echo $name; ?>
	</li>
<?php endforeach; ?>
</ol>

<input type="submit" name="submit" value="submit" />

</form>

<?php else: ?>
	No plugins to install.  Place new plugins in the 'public/plugins' directory in order for the application to recognize them.
<?php endif;?>
<?php foot(); ?>