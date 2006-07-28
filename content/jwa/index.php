<?php
$tags = $__c->tags()->getTags( 30 );
$maxCount = $__c->tags()->getMaxCount();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Katrina's Jewish Voices | An Online Collecting Project of the Jewish Women's Archive</title>
<?php include ('inc/metalinks.php'); ?>
</head>

<body id="home">
<a class="hide" href="#content">Skip to Content</a>
<div id="wrap">
	<?php include("inc/header.php"); ?>
	<div id="content">
		<h2>Home</h2>
		<div id="primary">

			<div id="featured-blurb" class="section">
				<?php for ($i=0; $i<1; $i++) {?>
					<?php 
						$featObject = $__c->objects()->findRandomFeaturedWithThumb();
					//	print_r($featObject);
						if( $featObject )
						{ 
						$featFile = $featObject->getRandomThumbnail();

						if ($featObject && $featFile):
					?>
					<h3>Featured Contribution: <a href="<?php echo $_link->to('object') . $featObject->object_id; ?>" class="featured-title-link"><?php echo $featObject->object_title; ?></a</h3>
					<div class="featured">
						<a href="<?php echo $_link->to('object') . $featObject->object_id; ?>"><?php	
							if( !empty( $featFile ) )
							{
								echo '<img src="'. WEB_VAULT_DIR.'/'.$featFile->file_archive_filename .'" width="426" />';
							}
						?></a>
					</div>


				<?php endif; } } ?>
				</div>

		
			<div id="tagcloud" class="section">
				<?php
				if( count($tags) == 0 ): ?>
				<h2 id="notice">No tags have been applied to objects.</h2>
				<?php else:
					echo '<h3>Browse Tags</h3>';
					$_html->tagCloud( $tags, $maxCount, $_link->to('browse'), 2 );
				endif;
				?>
			</div>
	
		</div>

		<div id="secondary">
			
			<div id="addyourvoice-blurb" class="section">
				<h3><a href="<?php echo $_link->to( 'contribute' ); ?>">Add Your Voice</a></h3>
				<p>Contribute to <cite>Katrina&#8217;s Jewish Voices</cite> by <a href="<?php echo $_link->to( 'contribute' ); ?>">adding your voice</a>.</p>		
			</div>
			<div id="login-box"><h3>
			<?php if (!self::$_session->getUser()) { ?>
				Want to create your own archive? <a href="<?php echo $_link->to('myarchive'); ?>">Sign in or sign up!</a>
			<?php } else { ?>
				Logged in as <?php echo self::$_session->getUser()->user_first_name.' '.self::$_session->getUser()->user_last_name; ?>
				<a href="<?php echo $_link->to('logout'); ?>">logout</a>
			<?php } ?></h3>
			</div>
			<div id="about-blurb" class="section">
				<h3>About <em>Katrina&#8217;s Jewish Voices</em></h3>
				<p>The <a href="http://jwa.org">Jewish Women&#8217;s Archive</a> organized <cite>Katrina&#8217;s Jewish Voices</cite> in collaboration with the <a href="http://chnm.gmu.edu">Center for History and New Media</a>. Through the contributions of individuals and organizations nationwide, the project is creating a virtual archive of stories, images, and reflections about the New Orleans and Gulf Coast Jewish communities before and after Hurricane Katrina.</p> 
				<p><a href="<?php echo $_link->to('about'); ?>">Read more about <cite>Katrina&#8217;s Jewish Voices</cite></a>.</p>
			</div>
			
		</div>
	</div>
	
<?php include("inc/footer.php"); ?>
</div>
</body>
</html>