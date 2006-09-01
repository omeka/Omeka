<?php
//Layout: default;
include( 'subnav.php' );
$t = new Tags;
$tags = $t->getTagsAndCount( 100, true, false, null, self::$_session->getUser()->getId() );
$max = $t->getMaxCount( self::$_session->getUser()->getId() );
$result = $__c->accounts()->findMyTaggedObjects();

?>
<div class="container">


<div id="mytags">
	<?php
		$_html->tagCloud( $tags, $max, $_link->to('account', 'tags'), 3, 1);
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
<?php endif; ?>
</div>