<?php head(); ?>

<?php foreach( $types as $key => $type ): ?>
	<?php echo ($key+1); ?>) <?php echo $type->name; ?><br/>
<?php endforeach; ?>

<?php footer(); ?>