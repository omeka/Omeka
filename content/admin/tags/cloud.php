<?php
//Layout: default;

$tags = new Tags;
$all = $tags->getTagsAndCount();
?>

<style type="text/css" media="screen">
	div { display: inline; }
	.tag-small { font-size:1em;}
	.tag-medium { font-size:1.6em;}
	.tag-large { font-size:2.2em;}
	.tag-xlarge { font-size:2.8em;}
	#notice {width:100%; margin:30px auto; text-align: center;}
</style>

<?php include( 'subnav.php' ); ?>
<br />
<div class="container">
<div id="mytags">
	<?php if( count($all) == 0 ): ?>
	<h2 id="notice">No tags have been applied to objects.</h2>
	<?php else:
		$_html->tagCloud( $all, $tags->getMaxCount(), $_link->to( 'objects', 'all' ), 2 );
	endif;
	?>
</div>
</div>