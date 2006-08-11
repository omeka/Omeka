<?php
// Layout: default;

$result = $__c->objects()->paginate();
?>

<script type="text/javascript" charset="utf-8">
// <![CDATA[
	function highlight( element ) {
		element.style.cursor = 'pointer';
		element.style.background = '#FFFDCE';
	}

	function unHighlight( element ) {
		element.style.background = '#fff';
	}
	
	function cursorIt( element ) {
		element.style.cursor = 'pointer';
	}

	function loadObject( id ) {
		window.location = '<?php echo $_link->to( "objects", "show" ); ?>' +id ;
	}
	
	function showView() {
		this.style.display = 'visible';
	}

// ]]>
</script>


<?php include( 'subnav.php' ); ?>
<div id="object-all">
	<div id="object-nav-wrapper">
		<form name="object_limit" method="get" action="<?php echo $_link->to( 'objects' ); ?>" >

		<select name="objectType">
			<option value="">Show by Category:&nbsp;</option>
			<option value="">All</option>
			<?php foreach( $__c->categories()->all( 'array' ) as $cat ): ?>
			<option value="<?php echo $cat['category_id'] ?>" <?php if( $cat['category_id'] ==  self::$_request->getProperty('objectType') ){ echo ' selected '; } ?>><?php echo $cat['category_name'] ?></option>
			<?php endforeach; ?>
		</select>
		<select name="collection">
			<option value="">Show by Collection:&nbsp;</option>
			<option value="">All</option>
			<?php foreach( $__c->collections()->all( 'array' ) as $coll ): ?>
			<option value="<?php echo $coll['collection_id']; ?>" <?php if( $coll['collection_id'] ==  self::$_request->getProperty('collection') ){ echo ' selected '; } ?>><?php echo $coll['collection_name']; ?></option>
			<?php endforeach; ?>
		</select>
		<select name="status">
			<option value="">Show by status:&nbsp;</option>
			<option value="">All</option>
			<option value="notyet" <?php if( self::$_request->getProperty('status') == "notyet"){ echo ' selected '; } ?>>Not yet considered</option>
			<option value="moreinfo" <?php if( self::$_request->getProperty('status') == "moreinfo"){ echo ' selected '; } ?>>Additional contributor information needed</option>
			<option value="review" <?php if( self::$_request->getProperty('status') == "review"){ echo ' selected '; } ?>>Administrative review needed</option>
			<option value="approved" <?php if( self::$_request->getProperty('status') == "approved"){ echo ' selected '; } ?>>Approved</option>
			<option value="rejected" <?php if( self::$_request->getProperty('status') == "rejected"){ echo ' selected '; } ?>>Rejected</option>
		</select>
		<input type="text" name="search" value="" style="width:100px;" onclick="this.value=''"/>
		<input type="submit" value="Search"/>
		</form>
	</div>

	<?php if( $result['objects']->total() == 0 ): ?>
	<h2 id="notice">No objects found.</h2>
	<?php else: ?>
	<div id="pagination-links">
	<?php 
		$_link->pagination(	$result['page'],
							$result['per_page'],
							$result['total'],
							'5',
							$_link->to( 'objects', 'all' ) );
	?>
	</div>
	<div id="objects-wrapper">
	<?php
		foreach( $result['objects'] as $object ):
		$object->getFilesWithThumbnails()
			   ->getCategoryMetadata()
			   ->getContributor();
	?>
	
	<div id="object-<?php echo $object->object_id; ?>" class="object">
		<div class="object-bar">#<?php echo $object->object_id; ?></div>
	    <div class="meta" onclick="loadObject(<?php echo $object->object_id; ?>)" onmouseover="highlight(this)" onmouseout="unHighlight(this)">
	        <h3><a href="<?php echo $_link->to('objects', 'show') . $object->object_id; ?>"><?php echo htmlentities( $object->object_title ); ?></a></h3>
			<ul class="object-metadata">
				<?php if( $object->category_name ): ?>
				<li class="object-type">Object Type: <?php echo $object->category_name; ?></li>
				<?php else: ?>
				<li class="object-type">Object Type: None</li>
				<?php endif; ?>
				<li>Files: <?php echo $object->getFileTotal(); ?></li>
	        </ul>
	    </div>
		<div class="details">
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
		<div class="object-tags">
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
	</div>
    

	<?php endforeach; endif; ?>
	</div>
</div>