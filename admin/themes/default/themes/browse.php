<?php head();?>
<?php common('settings-nav'); ?>
<div id="primary">
	<h1>Themes</h1>
<form method="post">
	<?php foreach($this->themes as $theme): ?>
	<div class="<?php echo 'theme';//if($theme == $current_admin) echo 'current_admin'; else echo 'theme';?>">
		<div class="meta">
		<label><input type="radio" name="public_theme" value="<?php echo $theme->directory; ?>" <?php if($current == $theme):  ?>checked="checked" <?php endif; ?>/>	
		<?php echo $theme->title; ?></label>
		<ul>
			<li><span class="type">Author:</span> <span class="value"><?php echo $theme->author; ?></span></li>
			<li><span class="type">License:</span> <span class="value"><?php echo $theme->license; ?></span></li>
			<li><span class="type">Website:</span> <span class="value"><a href="http://<?php echo $theme->website; ?>"><?php echo $theme->website; ?></a></span></li>
		</ul>
		</div>
		<div class="description">
		<img src="<?php echo $theme->image; ?>" width="180"/>
		<p><?php echo $theme->description; ?></p>
		</div>
	</div>
	<?php endforeach; ?>
	
	<input type="submit" name="submit" value="Switch this theme" />
</form>
</div>
<?php foot(); ?>