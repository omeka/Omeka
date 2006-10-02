<?php
//Layout: default;
include( 'subnav.php' );

$result = $__c->accounts()->getMyFavorites(3);
$mine = $__c->accounts()->getMyContributions(3);
$t = new Tags;
$tags = $t->getTagsAndCount( 100, true, false, null, self::$_session->getUser()->getId() );
$max = $t->getMaxCount( self::$_session->getUser()->getId() );
?>
<div id="mytags">
	<h2>My Recent Tags</h2>
	<?php
		$_html->tagCloud( $tags, $max, $_link->to('account', 'tags'), 3, 1);
	?>
</div>

<div id="my-items">
	<h2>My Recent Items</h2>
	<?php if( $mine['objects']->total() == 0 ): ?>
	<h2 id="notice">You have no favorites.</h2>
	<?php else: ?>
		<?php
			foreach( $mine['objects'] as $object ):
			$object->getFilesWithThumbnails()
			   	   ->getCategoryMetadata()
			   	   ->getContributor();
		?>
			<div id="object-<?php echo $object->object_id; ?>" class="object">
				<div class="object-title">
				<h3><a href="<?php echo $_link->to('objects', 'show') . $object->object_id; ?>">Item #<?php echo $object->object_id; ?>: <?php echo htmlentities( $object->object_title ); ?></a></h3>

				</div>

					<ul class="object-metadata">
						<?php if( $object->category_name ): ?>
						<li class="object-type">Item Type: <?php echo $object->category_name; ?></li>
						<?php else: ?>
						<li class="object-type">Item Type: None</li>
						<?php endif; ?>
						<li>Files: <?php echo $object->getFileTotal(); ?></li>
			        </ul>
				<div class="details">
					<span class="thumbnail-container">
					<?php
						$file_id = mt_rand( 0, ( $object->files->total() - 1 ) );
						$file = $object->files->getObjectAt( $file_id );
						if( !empty( $file->file_thumbnail_name ) ) {
							$_html->thumbnail( $file->file_thumbnail_name,
												array(	'class' => 'thumbnail',
												 		'alt'	=> $file->file_description,
												 		'title'	=> $file->file_title ),
												100 );
						}
					?>
					</span>
			        <p class="description">
						<?php
							if( $object->getDesc() )
							{
								echo htmlentities( $object->getShortDesc() );	
							}
							else
							{
								echo 'No description given.';
							}
						?>
					</p>
				</div>
					<ul class="tags">
						<?php
							$object->getTags();
							if( $object->tags->total() > 0 ):
						?>
							<li>Tags:</li>
							<?php foreach ($object->tags as $tag): ?>
							<li><a href="<?php echo $_link->to( 'objects', 'all' ); ?>?tags=<?php echo urlencode( $tag['tag_name'] ); ?>"><?php echo $tag['tag_name']; ?></a><?php if( $object->tags->nextIsValid() ) echo ','; ?></li>
							<?php endforeach;?>
						<?php else: ?>
							<li>Not Tagged.</li>
						<?php endif; ?>
					</ul>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>
</div>

<div id="my-favorites">
	<h2>My Recent Favorites</h2>
	<?php if( $result['objects']->total() == 0 ): ?>
	<h2 id="notice">You have no favorites.</h2>
	<?php else: ?>
		<?php
			foreach( $result['objects'] as $object ):
			$object->getFilesWithThumbnails();
		?>
			<div class="object">
				<h2 class="object-title"><a href="<?php echo $_link->to('objects', 'show') . $object->object_id; ?>"><?php echo $object->object_title; ?></a></h2>
				<p class="description">
					<span class="thumbnail-container">
					<?php
						$file_id = mt_rand( 0, ( $object->files->total() - 1 ) );
						$file = $object->files->getObjectAt( $file_id );
						if( !empty( $file->file_thumbnail_name ) ) {
							$_html->thumbnail( $file->file_thumbnail_name,
												array(	'class' => 'thumbnail',
												 		'alt'	=> $file->file_description,
												 		'title'	=> $file->file_title ),
												100 );
						}
					?>
					</span>
					<?php
						if( $object->object_description )
						{
							echo htmlentities( $object->short_desc );	
						}
						else
						{
							echo 'No description given.';
						}
					?>
				</p>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>
</div>