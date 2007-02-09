<?php foreach( $items as $key => $item ): ?>
	<?php  echo ($key+1);?>) <?php echo $item->title; ?><br/>
<?php endforeach; ?>