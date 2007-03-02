<?php head(); ?>

<ul id="secondary-nav" class="navigation">
	<?php nav(array('Browse' => uri('items'), 'Add Item' => uri('items/add')));?>
</ul>

<form id="search">
	<input type="text" name="search" />
	<input type="submit" name="submit" value="Search" />
</form>
<?php echo $pagination; ?>

<?php
//plugin('GeoLocation', 'map', null, null, 5, 200, 200, 'map', uri('json/map/browse'), array('clickable' => false) );
//plugin('GeoLocation', 'map', null, null, 5, 300, 200, 'map2', uri('json/map/browse'), array('clickable' => true) );
?>

<?php foreach( $items as $key => $item ): ?>
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
<?php foot(); ?>