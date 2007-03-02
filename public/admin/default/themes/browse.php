<?php head();?>
	<h2>Themes</h2>

	<?php foreach($this->themes as $theme): ?>
	<div class="theme">
		<div class="metainfo">
		<h3><?php echo $theme['ini']->title; ?></h3>
		<ul>
			<li><span class="type">Author:</span> <span class="value"><?php echo $theme['ini']->author; ?></span></li>
			<li><span class="type">License:</span> <span class="value"><?php echo $theme['ini']->license; ?></span></li>
			<li><span class="type">Website:</span> <span class="value"><a href="http://<?php echo $theme['ini']->website; ?>"><?php echo $theme['ini']->website; ?></a></span></li>
		</ul>
		</div>

		<p class="description"><?php echo $theme['ini']->description; ?></p>
		<img src="<?php echo $theme['image']; ?>"/>
		
	</div>
	<?php endforeach; ?>
<?php foot(); ?>