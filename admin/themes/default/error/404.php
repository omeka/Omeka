<?php 
	header ("HTTP/1.0 404 Not Found"); 
	set_items_for_loop(recent_items('10'));
	set_collections_for_loop(recent_collections('5'));
?>

<?php head(); ?>
<h1>404: Wha Huh?</h1>

<div id="primary" class="filenotfound">
    <?php echo flash(); ?>
    
	<p>You've tried to access a page that does not seem to exist. Sometimes this happens. Below is a quick break-down of the items, collections, and exhibits on this site. If this does not help, try contacting the <a href="#">site administrator</a>.</p>
	<div id="recent-items">
	<h2>Items</h2>
	<ul class="items">
		<?php while(loop_items()):?>
		<li class="item"><?php echo link_to_item(); ?></li>
		<?php endwhile; ?>
	</ul>
	</div>
	<div id="recent-collections">
	<h2>Collections</h2>
	<ul class="collections">
		<?php while (loop_collections()):?>
		<li class="collection"><?php echo link_to_collection(); ?></li>
		<?php endwhile; ?>
	</ul>
	</div>

</div>

<?php foot(); ?>
