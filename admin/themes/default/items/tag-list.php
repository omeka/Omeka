<h3>All Tags</h3>
<ul class="tags">
		<?php foreach( $item->Tags as $key => $tag ): ?>
		<li class="tag">
			<a href="<?php echo uri('items/browse/tag/'.$tag->name);?>" rel="<?php echo h($tag->id); ?>"><?php echo $tag; ?></a>
		</li>
		<?php endforeach; ?>
</ul>