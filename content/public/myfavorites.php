<?php
$result = $__c->accounts()->getMyFavorites();
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>My Archive | Katrina's Jewish Voices</title>
<?php include ('inc/metalinks.php'); ?>

</head>

<body id="myarchive" class="myfavorites">
<a class="hide" href="#content">Skip to Content</a>
<div id="wrap">
	<?php include("inc/header.php"); ?>
	<div id="content">
		<h2>MyArchive</h2>
		<?php include ('inc/secondarynav.php')?>
		<?php if( $result['objects']->total() == 0 ): ?>
		<div id="primary">
			<h3 id="notice">You have no favorites.</h3>
			<?php else: ?>
			<div id="page-links">
			<?php 
				$_link->pagination(	$result['page'],
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
					   ->getCategoryMetadata()
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
	</div>
	
<?php include("inc/footer.php"); ?>
</div>
</body>
</html>