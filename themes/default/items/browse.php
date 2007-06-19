<?php head(); ?>
<h1>Browse</h1>
<?php echo pagination(); ?>
<div id="primary">
<?php 
display_item_list($items,false,false); 
?>

</div>
<div id="secondary">
	<form id="search">
		<input type="text" name="search" />
		<input type="submit" name="submit" value="Search" />
	</form>
</div>
<?php foot(); ?>