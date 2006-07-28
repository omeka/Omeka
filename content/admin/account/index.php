<?php
//Layout: default;
include( 'subnav.php' );

$result = $__c->accounts()->getMyFavorites(3);
$mine = $__c->accounts()->getMyContributions(3);
$t = new Tags;
$tags = $t->getTagsAndCount( 100, true, false, null, self::$_session->getUser()->getId() );
$max = $t->getMaxCount( self::$_session->getUser()->getId() );
?>
<div class="container">
<div id="mytags">
	<h2>My Tags</h2>
	<?php
		$_html->tagCloud( $tags, $max, $_link->to('account', 'tags'), 3, 1);
	?>
</div>

<div id="my-contributions">
	<h2>Selected Contributions</h2>
	<?php if( $mine['objects']->total() == 0 ): ?>
	<h2 id="notice">You have no favorites.</h2>
	<?php else: ?>
		<?php
			foreach( $mine['objects'] as $object ):
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

<div id="my-favorites">
	<h2>Selected Favorites</h2>
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
</div>