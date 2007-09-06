<?php head(); ?>
<?php $tag = $_GET['tag']; ?>

	<div id="primary">
		<h1>Items</h1>
		<?php echo $tag;?>
			<div id="pagination"><?php echo pagination_links(); ?></div>
			<?php display_item_list($items,false,false); ?>
	</div>
<?php foot(); ?>