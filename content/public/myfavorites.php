<?php
// Layout: default;

$result = $__c->accounts()->getMyFavorites();
?>

		<h2>MyArchive</h2>
		<?php include( $_partial->file( 'secondarynav' ) ); ?>
		<?php if( $result['objects']->total() == 0 ): ?>
		<div id="primary">
			<h3 id="notice">You have no favorites.</h3>
			<?php else: ?>
			<div id="page-links">
			<?php 
				if ( $result['total'] > $result['per_page'] ) $_link->pagination(	$result['page'],
									$result['per_page'],
									$result['total'],
									'5',
									$_link->to( 'account', 'favorites' ) );
			?>
			</div>
			<div id="primary">
			<h3>MyFavorites</h3>
			<div id="object-list" class="stripe">
			<?php
				foreach( $result['objects'] as $object ):
				$object->getFilesWithThumbnails()
					   ->getTypeMetadata()
					   ->getContributor();
			?>

			<div id="object-<?php echo $object->object_id; ?>" class="object">
				<span class="object-bar hide">#<?php echo $object->object_id; ?></span>
			        <h4><a href="<?php echo $_link->to('object') . $object->object_id; ?>"><?php echo htmlentities( $object->object_title ); ?></a></h4>
					<?php
						$file_id = mt_rand( 0, ( $object->files->total() - 1 ) );
						$file = $object->files->getObjectAt( $file_id );
						if( !empty( $file->file_thumbnail_name ) ) {
							echo '<a href="'. $_link->to('object') . $object->object_id.'">';
							$_html->thumbnail( $file->file_thumbnail_name,
												array(	'class' => 'thumbnail',
												 		'alt'	=> $file->file_description,
												 		'title'	=> $file->file_title ),
												150 );
							echo '</a>';
						}
					?>        
			<p class="description">
						
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
					<ul class="tags">
						<?php
							$object->getTags();
							if( $object->tags->total() > 0 ):
						?>
							<li>Tags:</li>
							<?php foreach ($object->tags as $tag): ?>
							<li><a href="<?php echo $_link->to( 'account', 'favorites' ); ?>?tags=<?php echo urlencode( $tag['tag_name'] ); ?>"><?php echo $tag['tag_name']; ?></a><?php if( $object->tags->nextIsValid() ) echo ','; ?></li>
							<?php endforeach;?>
						<?php else: ?>
							<li>Not Tagged.</li>
						<?php endif; ?>
					</ul>
			</div>


			<?php endforeach; endif; ?>	
			</div>
		</div> 	