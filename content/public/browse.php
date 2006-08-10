<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Browse | Katrina's Jewish Voices</title>
<?php include ('inc/metalinks.php'); ?>

<script type="text/javascript" charset="utf-8">
// <![CDATA[
	function highlight( element ) {
		element.style.cursor = 'pointer';
		element.style.background = '#CEE5F3';
	}

	function unHighlight( element ) {
		element.style.background = '#eee';
	}
// ]]>
</script>
</head>

<body id="browse" class="intro">
<a class="hide" href="#content">Skip to Content</a>
<div id="wrap">
	<?php include("inc/header.php"); ?>
	<div id="content">
		<h2>Browse</h2>
		<?php include("inc/secondarynav.php"); ?>
		<div id="page-links">
		<?php
		
			$objects = $__c->objects()->paginate(true, 5);
			$_link->pagination(	$objects['page'],
								$objects['per_page'],
								$objects['total'],
								'5',
								$_link->to( 'browse' ) );
		?>
		</div>

		<div id="primary">

			<?php if (!$_SERVER['QUERY_STRING'] && $__c->objects()->getPageNum()<=1 ): ?>
					<?php 
						$featObject = $__c->objects()->findRandomFeaturedWithThumb();
						if( $featObject ):
							$featFile = $featObject->getRandomThumbnail();
					?>
					<h3>Featured Contribution</h3>
					<div class="featured-object">
						<h4 class="featured-title"><a href="<?php echo $_link->to('object') . $featObject->object_id; ?>"><?php if(!empty($featObject->object_title)): ?><?php echo $featObject->object_title; ?><?php else: ?>Untitled<?php endif; ?></a></h4>
						<a href="<?php echo $_link->to('object') . $featObject->object_id; ?>"><?php	$_html->thumbnail( $featFile->file_thumbnail_name,
												array(	'class' => 'featured-thumb',
												 		'alt'	=> $featFile->file_description,
												 		'title'	=> $featFile->file_title ),
												200 ); ?></a>
						<p class="featured-description"><?php echo $featObject->getShortDesc(500); ?></p>
							<ul class="taglist">
								<?php
									$featObject->getTags();
									if( $featObject->tags->total() > 0 ):
								?>
									<li>Tags:</li>
									<?php foreach ($featObject->tags as $tag): ?>
									<li><a href="<?php echo $_link->to( 'browse' ); ?>?tags=<?php echo urlencode( $tag['tag_name'] ); ?>"><?php echo $tag['tag_name']; ?></a><?php if( $featObject->tags->nextIsValid() ) echo ','; ?></li>
									<?php endforeach;?>
									</ul>
								<?php else: ?>
									<li>This object is currently not tagged.</li>
								<?php endif; ?>
								</ul>
					</div>
					<?php endif; ?>	
			<?php endif; ?>
			
		<div id="objects-list" class="stripe">
		<?php if( isset( $_REQUEST['tags'] ) ):?>
		<h3>You are viewing all objects tagged: <?php echo htmlentities( $_REQUEST['tags'] ); ?></h3>
		<?php elseif( isset( $_REQUEST['collection'] ) ):?>
		<h3><?php echo $__c->collections()->findById( $_REQUEST['collection'] )->collection_name;?></h3>
		<?php elseif( isset( $_REQUEST['contributor'] ) ): ?>
		<h3>Contributions by <?php echo $_REQUEST['contributor']; ?></h3>
		<?php elseif( isset( $_REQUEST['search'] ) ):?>
		<h3>Search Results</h3>
		<?php elseif( isset( $_REQUEST['featured'] ) ):?>
		<h3>Featured Objects</h3>
		<?php else:?>
		<h3>Recent Objects</h3>
		<?php endif; ?>
		
		<?php foreach( $objects['objects'] as $object ): $object->getFilesWithThumbnails(); ?>
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
		<?php endforeach; ?>
		</div>
		</div>
	</div>
<?php include("inc/footer.php"); ?>
</div>
</body>
</html>