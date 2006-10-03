<?php
//Layout: default;
include( 'subnav.php' );

$result = $__c->accounts()->getMyFavorites();
?>
<div class="container">

<?php /*
<script type="text/javascript" charset="utf-8">
// <![CDATA[
	function highlight( element ) {
		element.style.cursor = 'pointer';
		element.style.background = '#FFFDCE';
	}

	function unHighlight( element ) {
		element.style.background = '#fff';
	}
	
	function loadObject( id ) {
		window.location = '<?php echo $_link->to( "items", "show" ); ?>' +id ;
	}
</script>
*/ ?>
<h2>My Favorites</h2>
<?php if( $result['objects']->total() == 0 ): ?>
<h3 class="notice">You have no favorites.</h3>
<?php else: ?>
<div id="pagination-links">
<?php 
	$_link->pagination(	$result['page'],
						$result['per_page'],
						$result['total'],
						'5',
						$_link->to( 'account', 'favorites' ) );
?>
</div>

<?php
	foreach( $result['objects'] as $item ):
	$item->getFilesWithThumbnails()
		   ->getTypeMetadata()
		   ->getContributor();
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