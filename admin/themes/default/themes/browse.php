<?php head(array('title'=>'Themes', 'body_class'=>'themes'));?>
<?php common('settings-nav'); ?>
<div id="primary">
	<h1>Themes</h1>
    <?php echo flash(); ?>
<form method="post" id="themeswitch">
	<?php foreach($this->themes as $theme): ?>
	<div class="<?php echo 'theme';//if($theme == $current_admin) echo 'current_admin'; else echo 'theme';?>">
		<div class="meta">
		<label><input type="radio" name="public_theme" value="<?php echo h($theme->directory); ?>" <?php if($current == $theme):  ?>checked="checked" <?php endif; ?>/>	
		<?php echo h($theme->title); ?></label>
		<ul>
			<li><span class="type">Author:</span> <span class="value"><?php echo h($theme->author); ?></span></li>
			<li><span class="type">License:</span> <span class="value"><?php echo h($theme->license); ?></span></li>
			<li><span class="type">Website:</span> <span class="value"><a href="http://<?php echo h($theme->website); ?>"><?php echo h($theme->website); ?></a></span></li>
		</ul>
		</div>
		<div class="description">
		<img src="<?php echo h($theme->image); ?>" width="180"/>
		<p><?php echo h($theme->description); ?></p>
		</div>
	</div>
	<?php endforeach; ?>
	
	<input type="submit" name="submit" id="submit" value="Switch this theme" />
</form>
</div>
<?php foot(); ?>