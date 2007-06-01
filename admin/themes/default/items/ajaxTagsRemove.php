<?php 
	$item = get_item($_REQUEST['id']);
?>

<?php foreach( $item->Tags as $key => $tag ): ?>
	<li>
		<?php echo $tag->name; ?>
		<button type="submit" name="remove_tag" value="<?php echo $tag->id; ?>">[x]</button>
	</li>
<?php endforeach; ?>