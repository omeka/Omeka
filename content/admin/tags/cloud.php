<?php
//Layout: default;

$tags = new Tags;
$all = $tags->getTagsAndCount();
?>

<style type="text/css" media="screen">
	
</style>

<?php include( 'subnav.php' ); ?>
<br />
<div id="mytags">
	<?php if( count($all) == 0 ): ?>
	<h2 id="notice">No tags have been applied to objects.</h2>
	<?php else:
		$_html->tagCloud( $all, $tags->getMaxCount(), $_link->to( 'items', 'all' ), 2 );
	endif;
	?>
</div>