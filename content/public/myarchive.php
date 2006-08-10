<?php

if (!self::$_session->getUser()):
	header("Location: ".$_link->to('login'));
	exit;
endif;


$result = $__c->accounts()->getMyFavorites(2);
$mine = $__c->accounts()->getMyContributions(2);
$t = new Tags;
$tags = $t->getTagsAndCount( 100, true, false, null, self::$_session->getUser()->getId() );
$max = $t->getMaxCount( self::$_session->getUser()->getId() );

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>My Archive | Katrina's Jewish Voices</title>
<?php include ('inc/metalinks.php'); ?>

</head>

<body id="myarchive" class="intro">
<a class="hide" href="#content">Skip to Content</a>
<div id="wrap">
	<?php include("inc/header.php"); ?>
	<div id="content">
		<h2>MyArchive</h2>
		<?php include ('inc/secondarynav.php')?>
		<div id="primary">
			<div id="myfavorites" class="stripe">
				<h3>Selected Favorites</h3>

				<?php if( $result['objects']->total() == 0 ): ?>
				<h4 id="notice">You have no favorites.</h4>
				<?php else: ?>
					<?php
						foreach( $result['objects'] as $object ):
						$object->getFilesWithThumbnails();
					?>
						<div class="object">
							<h4 class="object-title"><a href="<?php echo $_link->to('object') . $object->object_id; ?>"><?php echo $object->object_title; ?></a></h4>
							
								<span class="thumbnail-container">
									<a href="<?php echo $_link->to('object') . $object->object_id; ?>">
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
								</a>
								</span>
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
							<ul class="taglist">
								<?php
									$object->getTags();
									if( $object->tags->total() > 0 ):
								?>
									<li>Tags:</li>
									<?php foreach ($object->tags as $tag): ?>
									<li><a href="<?php echo $_link->to( 'browse' ); ?>?tags=<?php echo urlencode( $tag['tag_name'] ); ?>"><?php echo $tag['tag_name']; ?></a><?php if( $object->tags->nextIsValid() ) echo ','; ?></li>
									<?php endforeach;?>
									</ul>
								<?php else: ?>
									<li>This object is currently not tagged.</li>
								<?php endif; ?>
								</ul>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
			<div id="mycontributions" class="stripe">
			<h3>Selected Contributions</h3>
				
			<?php if( $mine['objects']->total() == 0 ): ?>
			<h4 id="notice">You have no favorites.</h4>
			<?php else: ?>
				<?php
					foreach( $mine['objects'] as $object ):
					$object->getFilesWithThumbnails();
				?>
					<div class="object">
						<h4 class="object-title"><a href="<?php echo $_link->to('object') . $object->object_id; ?>"><?php echo $object->object_title; ?></a></h4>
						
							<span class="thumbnail-container">
								<a href="<?php echo $_link->to('object') . $object->object_id; ?>">
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
							</a>
							</span>
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
						<ul class="taglist">
							<?php
								$object->getTags();
								if( $object->tags->total() > 0 ):
							?>
								<li>Tags:</li>
								<?php foreach ($object->tags as $tag): ?>
								<li><a href="<?php echo $_link->to( 'browse' ); ?>?tags=<?php echo urlencode( $tag['tag_name'] ); ?>"><?php echo $tag['tag_name']; ?></a><?php if( $object->tags->nextIsValid() ) echo ','; ?></li>
								<?php endforeach;?>
								</ul>
							<?php else: ?>
								<li>This object is currently not tagged.</li>
							<?php endif; ?>
							</ul>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
			

			
			</div>
		</div>
		<div id="secondary">
			
				<h3>MyTags</h3>
				<div id="mytags">
				<?php
					$_html->tagCloud( $tags, $max, $_link->to('mytags'), 3, 1);
				?>
				</div>
		</div>
			
	</div>
	
<?php include("inc/footer.php"); ?>
</div>
</body>
</html>