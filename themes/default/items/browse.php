<?php head(); ?>
<h1>Browse</h1>
<?php echo pagination_links(); ?>
<div id="primary">

<?php foreach($items as $item): ?>

<h2><a href="<?php echo uri('items/show/'.$item->id); ?>"><?php echo $item->title; ?></a></h2>
<p><?php echo $item->description; ?></p>

<?php endforeach; ?>
</div>
<div id="secondary">
	<form id="search">
		<input type="text" name="search" />
		<input type="submit" name="submit" value="Search" />
	</form>
</div>
<?php foot(); ?>