<?php head(); ?>
<div id="content">
	<div id="installed">
<h2>List of plugins:</h2>

<ol>
<?php foreach( $plugins as $key => $plugin ): ?>
	<li><a href="<?php echo uri('plugins/show/'.$plugin->id); ?>"><?php echo $plugin->name; ?></a> </li>
<?php endforeach; ?>
</ol>
</div>
<div id="not-installed">
<?php //if(!empty($new_names)):?>
<h3>The following plugins have not yet been installed</h3>

<form method="post">


<?php/* foreach( $new_names as $name ): ?>
	<li>
		<input type="checkbox" name="<?php echo $name; ?>" />
		<?php  echo $name; ?>
	</li>
<?php endforeach; */?>


<input type="submit" name="submit" value="submit" />

</form>

<?php /*else: ?>
	No plugins to install.  Place new plugins in the 'public/plugins' directory in order for the application to recognize them.
<?php endif;*/?>
</div>
</div>

<?php foot(); ?>