<?php
// Layout: show;

$result = $__c->objects()->paginate();
?>
<ul id="sub-navigation" class="navigation subnav">
	<li<?php if(self::$_route['template'] == 'all') {echo ' class="current"';} ?>><a href="<?php echo $_link->to('objects'); ?>">Show Items</a></li>
	<li<?php if(self::$_route['template'] == 'add') {echo ' class="current"';} ?>><a href="<?php echo $_link->to('objects', 'add'); ?>">Add Item</a></li>
</ul>

<div id="object-all">
		<form name="object_limit" id="object-limit" method="get" action="<?php echo $_link->to( 'objects' ); ?>" >
			<label for="objectType">Object Type</label>
		<select name="objectType">
			<option value="">Show by Type:&nbsp;</option>
			<option value="">All</option>
			<?php foreach( $__c->types()->all( 'array' ) as $cat ): ?>
			<option value="<?php echo $cat['type_id'] ?>" <?php if( $cat['type_id'] ==  self::$_request->getProperty('objectType') ){ echo ' selected '; } ?>><?php echo $cat['type_name'] ?></option>
			<?php endforeach; ?>
		</select>
		<label for="collection">Collection</label>
		<select name="collection">
			<option value="">Show by Collection:&nbsp;</option>
			<option value="">All</option>
			<?php foreach( $__c->collections()->all( 'array' ) as $coll ): ?>
			<option value="<?php echo $coll['collection_id']; ?>" <?php if( $coll['collection_id'] ==  self::$_request->getProperty('collection') ){ echo ' selected '; } ?>><?php echo $coll['collection_name']; ?></option>
			<?php endforeach; ?>
		</select>
		<label for="status">Status</label>
		<select name="status">
			<option value="">Show by status:&nbsp;</option>
			<option value="">All</option>
			<option value="notyet" <?php if( self::$_request->getProperty('status') == "notyet"){ echo ' selected '; } ?>>Not Pubic</option>
			<option value="approved" <?php if( self::$_request->getProperty('status') == "approved"){ echo ' selected '; } ?>>Public</option>

		</select>
		<label for="featured">Featured</label>
		<select id="featured" name="featured">
			<option value="">All</option>
			<option value="0" <?php if( self::$_request->getProperty('featured') == "0"){ echo ' selected '; } ?>>Not featured</option>
			<option value="1" <?php if( self::$_request->getProperty('featured') == "1"){ echo ' selected '; } ?>>Featured</option>
		</select>
		<input type="text" name="search" value="" style="width:100px;" onclick="this.value=''"/>
		<input type="submit" value="Search"/>
		</form>


	<?php if( $result['objects']->total() == 0 ): ?>
	<h2 id="notice">No objects found.</h2>
	<?php else: ?>
	<h2 id="objects-results"><?php echo $result['total'];?> Results</h2>
	
	<div class="pagination navigation">
	<?php 
		$_link->pagination(	$result['page'],
							$result['per_page'],
							$result['total'],
							'5',
							$_link->to( 'objects', 'all' ) );
	?>
	</div>
	<div id="objects">
	<?php
		foreach( $result['objects'] as $object ):
		$object->getFilesWithThumbnails()
			   ->getTypeMetadata()
			   ->getContributor();
	?>
	
	<div id="object-<?php echo $object->object_id; ?>" class="object">
		<div class="object-title">
		<h3><a href="<?php echo $_link->to('objects', 'show') . $object->object_id; ?>">Item #<?php echo $object->object_id; ?>: <?php echo htmlentities( $object->object_title ); ?></a></h3>

		</div>
	        
			<ul class="object-metadata">
				<?php if( $object->type_name ): ?>
				<li class="object-type">Item Type: <?php echo $object->type_name; ?></li>
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
    

	<?php endforeach; endif; ?>
	</div>
	<div class="pagination navigation">
	<?php 
		$_link->pagination(	$result['page'],
							$result['per_page'],
							$result['total'],
							'5',
							$_link->to( 'objects', 'all' ) );
	?>
	</div>
	
</div>
