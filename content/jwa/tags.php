<?php
$tags = $__c->tags()->getTags();
$max = $__c->tags()->getMaxCount();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Tags | Katrina's Jewish Voices</title>
<?php include ('inc/metalinks.php'); ?>

</head>

<body id="browse" class="tags">
<a class="hide" href="#content">Skip to Content</a>
<div id="wrap">
	<?php include("inc/header.php"); ?>
	<div id="content">
		<h2>Browse</h2>
		<?php include("inc/secondarynav.php"); ?>

		<div id="primary">
			<h3>Tags</h3>
			<div id="tagcloud-full">
			<?php
				$_html->tagCloud( $tags, $max, $_link->to( 'browse' ), 4 );

			?>
			</div>
			<p class="info"><a href="<?php echo $_link->to('whataretags'); ?>" class="popup">What are tags?</a></p>
		</div>
	
		<div id="secondary">
		
		</div>
	</div>
	
<?php include("inc/footer.php"); ?>
</div>
</body>
</html>