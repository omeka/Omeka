<?php head(array('title' => 'Item'))?>

<div id="primary">

	<h2><?php echo $item->title; ?></h2>

	<div id="fullsizeimg">
		<?php if(fullsize($item)): echo fullsize($item); ?>
		<?php endif; ?>
	</div>

	<dl id="metadata">
		
		<dt id="desc">Description</dt>
		<dd>
			<?php if($item->description): echo $item->description; else: ?>
			<span>none available</span>
			<?php endif; ?>
		</dd>
		
		<dt>Publisher</dt>
		<dd id="publisher">
			<?php if($item->publisher): echo $item->publisher; else: ?>
			<span>none available</span>
			<?php endif; ?>
		</dd>
	
	<dt>Relation</dt>
		<dd id="relation">
			<?php if($item->relation): echo $item->relation; else: ?>
			<span>none available</span>
			<?php endif; ?>
		</dd>
	
	<dt>Language</dt>
		<dd id="language">
			<?php if($item->language): echo $item->language; else: ?>
			<span>none available</span>
			<?php endif; ?>
		</dd>
	
	<dt>Source</dt>
		<dd id="source">
			<?php if($item->source): echo $item->source; else: ?>
			<span>none available</span>
			<?php endif; ?>
		</dd>
	
	<dt>Subject</dt>
		<dd id="subject">
			<?php if($item->subject): echo $item->subject; else: ?>
			<span>none available</span>
			<?php endif; ?>
		</dd>

	<dt>Date</dt>
		<dd id="date">
			<?php if($item->date): echo $item->date; else: ?>
			<span>none available</span>
			<?php endif; ?>
		</dd>

	<?php foreach($item->Metatext as $key => $metatext): ?>
	<dt><?php echo $metatext->Metafield->name; ?></dt>
	<dd><?php if($metatext->text): echo $metatext->text; else: ?>
			<span>none available</span>
			<?php endif; ?></dd>
	<?php endforeach; ?>

	<dt>Tags</dt>
		<dd id="tags">
			<ul class="tags">
			<?php foreach( $item->Tags as $key => $tag ): ?>
				<li><a href="#" rel="tag"><?php echo $tag; ?></a></li>
			<?php endforeach; ?>
			</ul>
		</dd>
	
	</dl><!-- end metadata -->
	
</div><!-- end primary -->

</div><!-- end content -->

<?php foot(); ?>
