<?php 
	header ("HTTP/1.0 404 Not Found"); 
	$items = recent_items('10');
	$collections = recent_collections('5');
?>

<?php head(); ?>
<?php echo flash(); ?>
<div id="primary" class="filenotfound">
	<h1>404: Wha Huh?</h1>
	<p>You've tried to access a page that does not seem to exist. Sometimes this happens. Below is a quick break-down of the items, collections, and exhibits on this site. If this does not help, try contacting the <a href="#">site administrator</a>.</p>
	<div id="recent-items">
	<h2>Items</h2>
	<ul class="items">
		<?php foreach($items as $item):?>
		<li class="item"><?php link_to_item($item); ?></li>
		<?php endforeach; ?>
	</ul>
	</div>
	<div id="recent-collections">
	<h2>Collections</h2>
	<ul class="collections">
		<?php foreach($collections as $collection):?>
		<li class="collection"><?php link_to_collection($collection); ?></li>
		<?php endforeach; ?>
	</ul>
	</div>

</div>

<?php foot(); ?>
