<?php head();?>
<div id="content">
	<h2>Themes</h2>

		<?php foreach($this->themes as $theme): ?>
		<div class="theme">
		<h3><?php echo $theme['ini']->title; ?></h3>
		<ul>
			<li>Author: <?php echo $theme['ini']->author; ?></li>
			<li>Description: <?php echo $theme['ini']->description; ?></li>
			<li>License: <?php echo $theme['ini']->license; ?></li>
			<li>Website: <a href="http://<?php echo $theme['ini']->website; ?>"><?php echo $theme['ini']->website; ?></a></li>
			<li><img src="<?php echo $theme['image']; ?>"/></li>
		</ul>
		</div>
		<?php endforeach; ?>
		
		<div class="theme">
			<h3>Glorious Theme</h3>
			<ul>
				
		</div>
	</div>
</div>
<?php foot(); ?>