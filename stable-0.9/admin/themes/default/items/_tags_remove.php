<?php 
	if(!isset($item)) {
		$item = item($_REQUEST['id']);
	}
?>

<?php foreach( $item->Tags as $key => $tag ): ?>
	<li>
		<input type="image" src="<?php echo img('delete.gif'); ?>" name="remove_tag" value="<?php echo h($tag->id); ?>" />
		<?php echo h($tag->name); ?>
	</li>
<?php endforeach; ?>