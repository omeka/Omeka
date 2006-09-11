<?php 
// Layout: default;

//Implementation of non-nested collections (masked as of merge)
if(1==0):?>
<?php
$collections = $__c->collections()->all( 'array' );
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
			<h3>Collections</h3>
			<dl class="collectionlist">
			<?php foreach( $collections as $collection ): ?>
			<dt><a href="<?php echo $_link->to( 'browse' ); ?>?collection=<?php echo $collection['collection_id']; ?>"><?php echo $collection['collection_name']; ?></a> (<?php echo $__c->objects()->totalInCollection( $collection['collection_id'] ); ?>)</dt>
			<dd><?php echo $collection['collection_description']; ?></dd>
			<?php endforeach; ?>
			</dl>
		</div>
	
	</div>
	
<?php include("inc/footer.php"); ?>
</div>
</body>
</html>

<?	//END MASKING
	endif;?>

<?php
$collections = $__c->collections()->all( 'array' );
?>

<h2>Collections</h2>
<?php $__c->collections()->displayNested(ABS_CONTENT_DIR.PUBLIC_THEME_DIR.DS.'partials'.DS.'collection.php'); ?>
