<?php
// Layout: show;

$result = $__c->items()->paginate();
?>
<ul id="sub-navigation" class="navigation subnav">
	<li<?php if(self::$_route['template'] == 'all') {echo ' class="current"';} ?>><a href="<?php echo $_link->to('items'); ?>">Show Items</a></li>
	<li<?php if(self::$_route['template'] == 'add') {echo ' class="current"';} ?>><a href="<?php echo $_link->to('items', 'add'); ?>">Add Item</a></li>
</ul>

<div id="item-all">
		<form name="item_limit" id="item-limit" method="get" action="<?php echo $_link->to( 'items' ); ?>" >
			<label for="itemType">Item Type</label>
		<select name="itemType">
			<option value="">Show by Type:&nbsp;</option>
			<option value="">All</option>
			<?php foreach( $__c->types()->all( 'array' ) as $cat ): ?>
			<option value="<?php echo $cat['type_id'] ?>" <?php if( $cat['type_id'] ==  self::$_request->getProperty('itemType') ){ echo ' selected '; } ?>><?php echo $cat['type_name'] ?></option>
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
			<option value="0" <?php if( self::$_request->getProperty('status') == "0"){ echo ' selected '; } ?>>Not Public</option>
			<option value="1" <?php if( self::$_request->getProperty('status') == "1"){ echo ' selected '; } ?>>Public</option>

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
	<h2 id="notice">No items found.</h2>
	<?php else: ?>
	<h2 id="items-results"><?php echo $result['total'];?> Results</h2>
	
	<div class="pagination navigation">
	<?php 
		$_link->pagination(	$result['page'],
							$result['per_page'],
							$result['total'],
							'5',
							$_link->to( 'items', 'all' ) );
	?>
	</div>
	<div id="items">
	<?php
		foreach( $result['objects'] as $item ):
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
    

	<?php endforeach; endif; ?>
	</div>
	<div class="pagination navigation">
	<?php 
		$_link->pagination(	$result['page'],
							$result['per_page'],
							$result['total'],
							'5',
							$_link->to( 'items', 'all' ) );
	?>
	</div>
	
</div>
