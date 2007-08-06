<?php 
	$item = item($_REQUEST['id']);
?>

<?php foreach( $item->Tags as $key => $tag ): ?>
	<li>
		<?php echo h($tag->name); ?>
		<button type="submit" name="remove_tag" value="<?php echo h($tag->id); ?>">[x]</button>
	</li>
<?php endforeach; ?>