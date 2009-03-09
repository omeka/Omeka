<?php head(array('title'=>'Themes', 'content_class' => 'vertical-nav', 'bodyclass'=>'themes primary'));?>
<h1>Themes</h1>

<?php common('settings-nav'); ?>



<div id="primary">
    <?php echo flash(); ?>
	<h2>Current Theme: &#8220;<?php echo h($current->title); ?>&#8221;</h2>

<div id="current-theme">
	<img src="<?php echo h($current->image); ?>" width="342px" />
	
	<ul>
		<li><span class="type">Author:</span> <span class="value"><?php echo h($current->author); ?></span></li>
		<li><span class="type">License:</span> <span class="value"><?php echo h($current->license); ?></span></li>
		<li><span class="type">Website:</span> <span class="value"><a href="http://<?php echo h($current->website); ?>"><?php echo h($current->website); ?></a></span></li>
		<li><span class="type">Description:</span> <span class="value"><?php echo h($current->description); ?></span></li>
		
	</ul>
</div>

<h2>Change Theme</h2>
<form method="post" id="themeswitch">
	<div class="themes">
	<?php foreach($this->themes as $theme): ?>
	<div class="theme<?php if($current == $theme) echo ' current-theme';?>">
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
		<img src="<?php echo h($theme->image); ?>" width="296px"/>
		</div>
	</div>
	<?php endforeach; ?>
	</div>
	<input type="submit" name="submit" class="submit submit-medium" id="submit" value="Switch Theme" />
</form>

<p class="managethemes">Add new themes by downloading them from the <a href="http://omeka.org/add-ons/themes/">Omeka Theme Directory</a>, or <a href="http://omeka.org/codex/Theme_Writing_Best_Practices">design your own</a>!</p>
</div>
<?php foot(); ?>