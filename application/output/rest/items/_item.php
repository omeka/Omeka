	
	<title><?php echo htmlspecialchars($item->title); ?></title>
	<publisher><?php echo htmlspecialchars($item->publisher); ?></publisher>
	<language><?php echo htmlspecialchars($item->language); ?></language>
	<relation><?php echo htmlspecialchars($item->relation); ?></relation>
	<spatial_coverage><?php echo htmlspecialchars($item->spatial_coverage); ?></spatial_coverage>
	<rights><?php echo htmlspecialchars($item->rights); ?></rights>
	<description><?php echo htmlspecialchars($item->description); ?></description>
	<source><?php echo htmlspecialchars($item->source); ?></source>
	<subject><?php echo htmlspecialchars($item->subject); ?></subject>
	<creator><?php echo htmlspecialchars($item->creator); ?></creator>
	<additional_creator><?php echo htmlspecialchars($item->additional_creator); ?></additional_creator>
	<date><?php echo htmlspecialchars($item->date); ?></date>	
	<added><?php echo htmlspecialchars($item->added); ?></added>
	<modified><?php echo htmlspecialchars($item->modified); ?></modified>
	<type_id><?php echo htmlspecialchars($item->type_id); ?></type_id>
	<collection_id><?php echo htmlspecialchars($item->collection_id); ?></collection_id>
	<contributor><?php echo htmlspecialchars($item->contributor); ?></contributor>
	<provenance><?php echo htmlspecialchars($item->provenance); ?></provenance>
	<citation><?php echo htmlspecialchars($item->citation); ?></citation>
	<temporal_coverage_start><?php echo htmlspecialchars($item->temporal_coverage_start); ?></temporal_coverage_start>
	<temporal_coverage_end><?php echo htmlspecialchars($item->temporal_coverage_end); ?></temporal_coverage_end>
	<tags><?php foreach ($item->Tags as $tag): ?><tag name="<?php echo htmlspecialchars($tag->name); ?>"></tag><?php
	endforeach;
	?></tags>

<?php if($item->Files->count()): ?>
	<files>
	<?php foreach ($item->Files as $file): ?>
	<file><title><?php echo htmlspecialchars($file->title); ?></title></file>
	<?php
	endforeach;
	?>
	</files>
<?php endif; ?>

	<collection><?php echo htmlspecialchars($item->Collection->name); ?></collection>
	
	<type><?php echo htmlspecialchars($item->Type->name); ?></type>
	
	<metafields><?php foreach($item->TypeMetadata as $name => $value): ?><metatext name="<?php echo htmlspecialchars($name); ?>"><?php echo htmlspecialchars($value); ?></metatext><?php endforeach; ?></metafields>
