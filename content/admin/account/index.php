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
			foreach( $mine['objects'] as $item ):
			$item->getFilesWithThumbnails()
			   	   ->getTypeMetadata();
		?>
			<div id="item-<?php echo $item->item_id; ?>" class="item">
				<div class="item-title">
				<h3><a href="<?php echo $_link->to('items', 'show') . $item->item_id; ?>">Item #<?php echo $item->item_id; ?>: <?php echo htmlentities( $item->item_title ); ?></a></h3>

				</div>

					<ul class="item-metadata">
						<?php if( $item->type_name ): ?>
						<li class="item-type">Item Type: <?php echo $item->type_name; ?></li>
						<?php else: ?>
						<li class="item-type">Item Type: None</li>
						<?php endif; ?>
						<li>Files: <?php echo $item->getFileTotal(); ?></li>
			        </ul>
				<div class="details">
					<span class="thumbnail-container">
					<?php
						$file_id = mt_rand( 0, ( $item->files->total() - 1 ) );
						$file = $item->files->getObjectAt( $file_id );
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
							if( $item->getDesc() )
							{
								echo htmlentities( $item->getShortDesc() );	
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
							$item->getTags();
							if( $item->tags->total() > 0 ):
						?>
							<li>Tags:</li>
							<?php foreach ($item->tags as $tag): ?>
							<li><a href="<?php echo $_link->to( 'items', 'all' ); ?>?tags=<?php echo urlencode( $tag['tag_name'] ); ?>"><?php echo $tag['tag_name']; ?></a><?php if( $item->tags->nextIsValid() ) echo ','; ?></li>
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
			foreach( $result['objects'] as $item ):
			$item->getFilesWithThumbnails();
		?>
			<div class="item">
				<h2 class="item-title"><a href="<?php echo $_link->to('items', 'show') . $item->item_id; ?>"><?php echo $item->item_title; ?></a></h2>
				<p class="description">
					<span class="thumbnail-container">
					<?php
						$file_id = mt_rand( 0, ( $item->files->total() - 1 ) );
						$file = $item->files->getObjectAt( $file_id );
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
						if( $item->item_description )
						{
							echo htmlentities( $item->short_desc );	
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