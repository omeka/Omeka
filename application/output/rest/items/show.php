<?php
/* This is really dumb;
// the short <?= php syntax interferes with the xml header!! */
// check to see if item is public or private
?>
<item id="<?php echo $item->id; ?>">
	
	<title><?php echo $item->title; ?></title>
	<publisher><?php echo $item->publisher; ?></publisher>
	<language><?php echo $item->language; ?></language>
	<relation><?php echo $item->relation; ?></relation>
	<spatial_coverage><?php echo $item->spatial_coverage; ?></spatial_coverage>
	<rights><?php echo $item->rights; ?></rights>
	<description><?php echo $item->description; ?></description>
	<source><?php echo $item->source; ?></source>
	<subject><?php echo $item->subject; ?></subject>
	<creator><?php echo $item->creator; ?></creator>
	<additional_creator><?php echo $item->additional_creator; ?></additional_creator>
	<date><?php echo $item->date; ?></date>	
	<added><?php echo $item->added; ?></added>
	<modified><?php echo $item->modified; ?></modified>
	<type_id><?php echo $item->type_id; ?></type_id>
	<collection_id><?php echo $item->collection_id; ?></collection_id>
	<user_id><?php echo $item->user_id; ?></user_id>
<? /* 	<contributer><?php echo $item->contributer; ?></contributer>*/?>
	<provenance><?php echo $item->provenance; ?></provenance>
	<citation><?php echo $item->citation; ?></citation>
	<temporal_coverage_start><?php echo $item->temporal_coverage_start; ?></temporal_coverage_start>
	<temporal_coverage_end><?php echo $item->temporal_coverage_end; ?></temporal_coverage_end>
	<tags>
	<?php foreach ($item->Tags as $tag): ?>
		<tag name="<?php echo $tag->name; ?>"></tag>
	<?php
	endforeach;
	?>
	</tags>

<?php if($item->Files->count()): ?>
	<files>
	<?php foreach ($item->Files as $file): ?>
	<file><title><?php echo $file->title; ?></title></file>
	<?php
	endforeach;
	?>
	</files>
<?php endif; ?>

	<collection><?php echo $item->Collection->name; ?></collection>
	
	<type><?php echo $item->Type->name; ?></type>
	
	<metafields><?php foreach($item->TypeMetadata as $name => $value): ?><metatext name="<?php echo $name; ?>"><?php echo $text; ?></metatext><?php endforeach; ?></metafields>
</item>