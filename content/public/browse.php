<?php
// Layout: default;
?>
<?php include( $_partial->file( 'secondarynav' ) ); ?>
		<div class="page-links">
		<?php
		
			$objects = $__c->objects()->paginate(true, 25);
			
			if ( $objects['total'] > $objects['per_page'] ) $_link->pagination(	$objects['page'],
								$objects['per_page'],
								$objects['total'],
								'5',
								$_link->to( 'browse' ) );
		?>
		</div>

		<div id="objects-list" class="stripe">
		<?php if( isset( $_REQUEST['tags'] ) ):?>
		<h3>You are viewing all objects tagged: <?php echo htmlentities( $_REQUEST['tags'] ); ?></h3>
		<?php elseif( isset( $_REQUEST['collection'] ) ):?>
		<?php 
			$collection = $__c->collections()->findById( $_REQUEST['collection'] );
				echo '<h3>';
				if ($collection->collection_parent) 
				{
					$collectionParent = $__c->collections()->findById( $collection->collection_parent );
					echo '<span id="collectonParent"><a href="'.$_link->to( 'browse' ).'?collection='.$collectionParent->collection_id.'">'.$collectionParent->collection_name.'</a>: </span>';
				}
				echo $collection->collection_name.'</h3>';
				echo '<p>'.$collection->collection_description.'</p>';
				$__c->collections()->displayNested(ABS_CONTENT_DIR.PUBLIC_THEME_DIR.DS.'partials'.DS.'collection.php', TRUE, null, $collection->getId()); ?>
	
		<?php elseif( isset( $_REQUEST['contributor'] ) ): ?>
		<h3>Contributions by <?php echo $_REQUEST['contributor']; ?></h3>
		<?php elseif( isset( $_REQUEST['search'] ) ):?>
		<h3>Search Results (<?php echo $objects['total'];?> Results)</h3>
		<?php elseif( isset( $_REQUEST['featured'] ) ):?>
		<h3>Featured Objects</h3>
		<?php else:?>
		<h3>Newest:</h3>
		<?php endif; ?>
		
		<?php foreach( $objects['objects'] as $object ): $object->getFiles(); $object->getContributor();	?>
		
			<div class="object">
				<div class="details">added by <?php echo $object->contributor->contributor_first_name . ' ' . $object->contributor->contributor_last_name; ?> on <?php echo date('d M Y (h:i a \E\S\T)', strtotime($object->object_modified) ); ?></div>
				<h4 class="title"><a href="<?php echo $_link->to('object') . $object->object_id; ?>"><?php echo $object->object_title; ?></a></h4>
				<?php if ($object->getShortDesc() != ''): ?><p class="abstract"><em>Abstract:</em> <?php echo $object->getShortDesc(); ?></p><?php endif; ?>
				
				<!-- code to iterate through files (for thumbnails) here
						<?php if( $object->files->total() > 0 ): 

						$file = $object->files->getObjectAt( 0 );
						if( !empty( $file->file_thumbnail_name ) ) {
							$_html->thumbnail( $file->file_thumbnail_name,
												array(	'alt'	=> $file->file_description,
												 		'title'	=> $file->getShortDesc(100) ),
												100 );
						}
						else echo $object->object_title; 
						endif; ?> -->
		
			</div>

		<?php endforeach; ?>
		</div>

		
		<div class="page-links">
		<?php
		
			if ( $objects['total'] > $objects['per_page'] ) $_link->pagination(	$objects['page'],
								$objects['per_page'],
								$objects['total'],
								'5',
								$_link->to( 'browse' ) );
		?>
		</div>
