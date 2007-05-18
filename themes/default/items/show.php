<?php head(array('title' => 'Item'))?>

<?php error($item);?>
<h2><?php echo $item->title; ?></h2>

<div id="core-metadata">

<h3>Core Metadata</h3>
	
	
	<h4>Description</h4>
	<div id="description"><?php echo $item->description; ?></div>

	<h4>Publisher</h4>
	<div id="publisher"><?php echo $item->publisher?></div>
	
	<h4>Relation</h4>
	<div id="relation"><?php echo $item->relation;?></div>
	
	<h4>Language</h4>
	<div id="language"><?php echo $item->language;?></div>
	
	<h4>Spatial Coverage</h4>
	<div id="coverage" class="editable" rel="text">
	<?php display_empty($item->spatial_coverage)?>
	</div>
	
	<h4>Temporal Coverage</h4>
	<?php display_empty($item->temporal_coverage_start); ?> &mdash; 
	<?php display_empty($item->temporal_coverage_end)?>


	<h4>Rights</h4>
	<div id="rights"><?php echo $item->rights;?></div>
	
	<h4>Source</h4>
	<div id="source"><?php echo $item->source;?></div>
	
	<h4>Subject</h4>
	<div id="subject"><?php echo $item->subject;?></div>

	<h4>Creator</h4>
	<div id="creator"><?php echo $item->creator;?></div>
	
	<h4>Additional Creator</h4>
	<div id="additional_creator"><?php echo $item->additional_creator;?></div>
	
	<h4>Date</h4>
	<div id="date"><?php echo $item->date;?></div>

</div>
<div id="additional-metadata">
	<h3>Type Metadata</h3>
	<div id="type-metadata">
		<?php foreach($item->Metatext as $key => $metatext): ?>
		<h4><?php echo $metatext->Metafield->name; ?></h4>
		<div><?php echo $metatext->text; ?></div>
		<?php endforeach; ?>
	</div>

	<h3>Tags</h3>
	<div id="tags">
		<ul class="tags">
			<?php foreach( $item->Tags as $key => $tag ): ?>
				<li><a href="#" rel="tag"><?php echo $tag; ?></a></li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
<?php foot();?>
