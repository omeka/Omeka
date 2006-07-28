<?php
	$map_objects = $__c->objects()->getMapObjects();
	$featured = $__c->objects()->getRandomMapFeatured();
	$featured->getTags();
	$featured->getFilesWithThumbnails();
	header('Content-type: text/xml');
?>
<mapitems page="<?php echo $map_objects['page']; ?>" per_page="<?php echo $map_objects['per_page']; ?>" total="<?php echo $map_objects['total']; ?>">
	<?php foreach( $map_objects['objects'] as $object): $object->getTags(); $object->getFilesWithThumbnails(); ?>
	<item id="<?php echo $object->getId(); ?>" latitude="<?php echo $object->latitude; ?>" longitude="<?php echo $object->longitude; ?>">
		<title><?php echo $object->object_title; ?></title>
		<desc><?php echo $object->object_description; ?></desc>
		<short_desc><?php echo $object->short_desc; ?></short_desc>
		<tags>
			<?php foreach( $object->tags as $tag ):?>
			<tag tag_id="<?php echo $tag['tag_id']?>" tag_name="<?php echo $tag['tag_name']; ?>" tag_count="<?php echo $tag['tagCount']; ?>" />
			<?php endforeach; ?>
		</tags>
		<files>
			<?php foreach( $object->files as $file ): ?>
			<file file_title="<?php echo $file->file_title; ?>" file_archive_filename="<?php echo $file->file_archive_filename; ?>" file_thumbnail_name="<?php echo $file->file_thumbnail_name; ?>" />
			<?php endforeach; ?>
		</files>
	</item>
	<?php endforeach;?>
</mapitems>