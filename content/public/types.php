<?php
$categories = $__c->categories()->all( 'array' );
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Collection | Katrina's Jewish Voices</title>
<?php include ('inc/metalinks.php'); ?>
</head>

<body id="browse" class="collection">
<a class="hide" href="#content">Skip to Content</a>
<div id="wrap">
	<?php include("inc/header.php"); ?>
	<div id="content">
		<?php include("inc/secondarynav.php"); ?>
		<div id="primary">
			<h3>Object Types</h3>
			<dl class="typeslist">
			<?php foreach( $categories as $category ): ?>
			<dt><a href="<?php echo $_link->to( 'browse' ); ?>?type=<?php echo $category['category_id']; ?>"><?php echo $category['category_name']; ?></a> (<?php echo $__c->objects()->totalInCategory( $category['category_id'] ); ?>)</dt>
			<dd><?php echo $category['category_description']; ?></dd>
			<?php endforeach; ?>
			</dl>
		</div>
	
	</div>
	
<?php include("inc/footer.php"); ?>
</div>
</body>
</html>