<?php
//Layout: default;
include( 'subnav.php' );
$t = new Tags;
$tags = $t->getTagsAndCount( 100, true, false, null, self::$_session->getUser()->getId() );
$max = $t->getMaxCount( self::$_session->getUser()->getId() );
$result = $__c->accounts()->findMyTaggedItems();

?>
<h2>My Tags</h2>
<div id="mytags">
	<?php
		$_html->tagCloud( $tags, $max, $_link->to('account', 'tags'), 3, 1);
	?>
</div>

<?php if( $result ): ?>
<?php if( $result['objects']->total() == 0 ): ?>
<h2 id="notice">No items found.</h2>
<?php elseif( $result['objects']->total() >= 0 ): ?>
<div id="pagination-links">
<?php 
	$_link->pagination(	$result['page'],
						$result['per_page'],
						$result['total'],
						'5',
						$_link->to( 'items', 'all' ) );
?>
</div>
<div id="objects-wrapper">
<?php
	foreach( $result['objects'] as $item ):
	$item->getFilesWithThumbnails()
		   ->getTypeMetadata()
		   ->getContributor();
?>

<div id="item-<?php echo $item->item_id; ?>" class="item">
	<div class="item-bar">#<?php echo $item->item_id; ?></div>
    <div class="meta" onclick="loadObject(<?php echo $item->item_id; ?>)" onmouseover="highlight(this)" onmouseout="unHighlight(this)">
        <h3><a href="<?php echo $_link->to('items', 'show') . $item->item_id; ?>"><?php echo htmlentities( $item->item_title ); ?></a></h3>
		<ul class="item-metadata">
			<?php if( $item->type_name ): ?>
			<li class="item-type">Item Type: <?php echo $item->type_name; ?></li>
			<?php else: ?>
			<li class="item-type">Item Type: None</li>
			<?php endif; ?>
			<li>Files: <?php echo $item->getFileTotal(); ?></li>
        </ul>
    </div>
	<div class="details">
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
	<div class="item-tags">
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
</div>


<?php endforeach; endif; ?>
</div>
<?php endif; ?>
</div>