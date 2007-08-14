<?php foreach( $section->Pages as $key => $page ): ?>
<li id="page_<?php echo $key; ?>" class="page">
	<div class="handle"><?php exhibit_layout($page->layout, false); ?></div>
	<div class="page-links">
	<span class="page-order"><?php text(array('name'=>"Pages[$key][order]",'size'=>2), $key); ?></span>
	<span><a href="<?php echo uri('exhibits/editPage/'.$page->id); ?>">[Edit]</a></td>
	<span><a href="<?php echo uri('exhibits/deletePage/'.$page->id); ?>" class="delete-page">[Delete]</a>
	</div>
</li>
<?php endforeach; ?>