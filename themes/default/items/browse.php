<?php head(); ?>
<h2>Browse</h2>
<?php echo pagination(); ?>
<div id="primary">
<?php foreach($items as $key => $item): ?>
<div class="item hentry">
	<h3><a href="<?php echo uri('items/show/'.$item->id); ?>" class="permalink">Item <?php echo $item->id;?>: <span class="entry-title"><?php echo $item->title; ?></span></a></h3>
	<p><?php echo $item->description; ?></p>
	
	<div class="tagcloud">Tags: 
	<?php if(count($item->Tags)): ?>
	<?php foreach ($item->Tags as $tag): ?>
	<a href="<?php echo uri('items/browse/tag/'.$tag->name); ?>" re="tag"><?php echo $tag ?></a>
	<?php endforeach; ?>
	<?php else: ?>
	No Tags
	<?php endif;?>
	
	</div>
</div>
<?php endforeach; ?>

</div>
<div id="secondary">
	<form id="search">
		<input type="text" name="search" />
		<input type="submit" name="submit" value="Search" />
	</form>
</div>
<?php foot(); ?>