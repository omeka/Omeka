<?php head();?>
	<h2>Themes</h2>

	<?php foreach($this->themes as $theme): ?>
	<div class="<?php echo 'theme';//if($theme == $current_admin) echo 'current_admin'; else echo 'theme';?>">
		
		<div class="metainfo">
		
		<h3><?php echo $theme->title; ?></h3>
		<ul>
			<li><span class="type">Author:</span> <span class="value"><?php echo $theme->author; ?></span></li>
			<li><span class="type">License:</span> <span class="value"><?php echo $theme->license; ?></span></li>
			<li><span class="type">Website:</span> <span class="value"><a href="http://<?php echo $theme->website; ?>"><?php echo $theme->website; ?></a></span></li>
		</ul>
		</div>

		<img src="<?php echo $theme->image; ?>" width="150"/><p class="description"><?php echo $theme->description; ?></p>
		
	</div>
	<?php endforeach; ?>
<?php foot(); ?>