<?php
//Layout: default;
include( 'subnav.php' );

$result = $__c->accounts()->getMyContributions();
?>

<div class="container">


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
		window.location = '<?php echo $_link->to( "objects", "show" ); ?>' +id ;
	}
</script>

<style type="text/css">
	#object-all {margin: 0; padding: 8px 0; width: 660px;}
	#objects-wrapper {width: 660px; margin: auto;}
	#object-nav-wrapper {width:660px; margin:auto; text-align: right; margin: 10px 0;}
	
	/* Pagination
	------------------------------------------*/
	#pagination-links {margin: 4px 0px; width:660px; text-align: right; margin: 10px 0;}
	#notice {width:100%; margin:30px auto; text-align: center;}

</style>

<?php if( $result['objects']->total() == 0 ): ?>
<h2 id="notice">You have no contributions.</h2>
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
	foreach( $result['objects'] as $object ):
	$object->getFilesWithThumbnails()
		   ->getTypeMetadata()
		   ->getContributor();
?>

<div id="object-<?php echo $object->object_id; ?>" class="object">
	<div class="object-bar">#<?php echo $object->object_id; ?></div>
    <div class="meta" onclick="loadObject(<?php echo $object->object_id; ?>)" onmouseover="highlight(this)" onmouseout="unHighlight(this)">
        <h3><a href="<?php echo $_link->to('objects', 'show') . $object->object_id; ?>"><?php echo htmlentities( $object->object_title ); ?></a></h3>
		<ul class="object-metadata">
			<?php if( $object->type_name ): ?>
			<li class="object-type">Object Type: <?php echo $object->type_name; ?></li>
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
				<li><a href="<?php echo $_link->to( 'account', 'favorites' ); ?>?tags=<?php echo urlencode( $tag['tag_name'] ); ?>"><?php echo $tag['tag_name']; ?></a><?php if( $object->tags->nextIsValid() ) echo ','; ?></li>
				<?php endforeach;?>
			<?php else: ?>
				<li>Not Tagged.</li>
			<?php endif; ?>
		</ul>
	</div>
</div>


<?php endforeach; endif; ?>
</div>