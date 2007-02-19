<form id="search">
	<input type="text" name="search" />
	<input type="submit" name="submit" value="Search" />
</form>

<?php echo $pagination; ?>
<?php foreach( $items as $key => $item ): ?>
	<?php  echo $item->id;?>) <?php echo $item->title; ?><br/>
<?php endforeach; ?>