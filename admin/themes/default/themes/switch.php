<?php
head();
?>

<form>
	<?php foreach( $themes as $key => $theme ): ?>
		<label for="<?php echo $theme->directory; ?>"><?php echo $theme->title;?>
			<input type="radio" name="theme" id="<?php echo $theme->directory;?> "value="<?php echo $theme->directory; ?>" /> 
		</label>
	<?php endforeach; ?>
	
	<input type="submit" name="submit" value="Switch the theme" />
</form>
<?php
foot();
?>
