<?php
$t = new Tags;
$tags = $t->getTagsAndCount( 100, true, false, null, self::$_session->getUser()->getId() );
$max = $t->getMaxCount( self::$_session->getUser()->getId() );
$result = $__c->accounts()->findMyTaggedObjects();
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>My Archive | Katrina's Jewish Voices</title>
<?php include ('inc/metalinks.php'); ?>

</head>

<body id="myarchive" class="mytags">
<a class="hide" href="#content">Skip to Content</a>
<div id="wrap">
	<?php include("inc/header.php"); ?>
	<div id="content">
		<h2>MyArchive</h2>
		<?php include ('inc/secondarynav.php')?>
		<div id="primary">
			<h3>MyTags</h3>
		
			<div id="mytags">
				<?php
					$_html->tagCloud( $tags, $max, $_link->to('mytags'), 3, 1);
				?>
			</div>

			

	
	
	
	<?php if( $result ): ?>
	<?php if( $result['objects']->total() == 0 ): ?>
	<h2 id="notice">No objects found.</h2>
	<?php elseif( $result['objects']->total() >= 0 ): ?>
	<div id="pagination-links">
	<?php 
		$_link->pagination(	$result['page'],
							$result['per_page'],
							$result['total'],
							'5',
							$_link->to( 'objects', 'all' ) );
	?>
	</div>
	<div id="objects-list" class="stripe">
	<h3>You are viewing all objects you have tagged: <?php echo htmlentities( $_REQUEST['tags'] ); ?></h3>
	
	<?php foreach( $result['objects'] as $object ): $object->getFilesWithThumbnails(); ?>
		<div class="object">
		<h4 class="object-title"><a href="<?php echo $_link->to('object') . $object->object_id; ?>"><?php if(!empty($object->object_title)): ?><?php echo $object->object_title; ?><?php else: ?>Untitled<?php endif; ?></a></h4>
			<div class="object-meta">
				<a href="<?php echo $_link->to('object') . $object->object_id; ?>"><?php
					$file_id = mt_rand( 0, ( $object->files->total() - 1 ) );
					$file = $object->files->getObjectAt( $file_id );
					if( !empty( $file->file_thumbnail_name ) ) {
						$_html->thumbnail( $file->file_thumbnail_name,
											array(	'class' => 'thumbnail',
											 		'alt'	=> $file->file_description,
											 		'title'	=> $file->file_title ),
											200 );
					}
				?></a>
			</div>
			<?php if(!empty($object->object_description)): ?>
			<div class="object-description">
				<p><?php echo htmlentities( $object->getShortDesc(500) ); ?></p>
			</div>
			<?php endif; ?>
			<ul class="taglist">
				<?php
					$object->getTags();
					if( $object->tags->total() > 0 ):
				?>
					<li>Tags:</li>
					<?php foreach ($object->tags as $tag): ?>
					<li><a href="<?php echo $_link->to( 'browse' ); ?>?tags=<?php echo urlencode( $tag['tag_name'] ); ?>"><?php echo $tag['tag_name']; ?></a><?php if( $object->tags->nextIsValid() ) echo ','; ?></li>
					<?php endforeach;?>
					</ul>
				<?php else: ?>
					<li>This object is currently not tagged.</li>
				<?php endif; ?>
				</ul>
		</div>
	<?php endforeach; endif; ?>
	</div>

	<?php endif; ?>
	
	</div> </div>
	
<?php include("inc/footer.php"); ?>
</div>
</body>
</html>