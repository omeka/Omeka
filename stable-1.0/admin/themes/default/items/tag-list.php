<h3>All Tags</h3>
<ul class="tags">
		<?php foreach( $item->Tags as $key => $tag ): ?>
		<li class="tag">
			<a href="<?php echo uri('items/browse/tag/'.html_escape($tag->name));?>" rel="<?php echo html_escape($tag->id); ?>"><?php echo html_escape($tag); ?></a>
		</li>
		<?php endforeach; ?>
</ul>