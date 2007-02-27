<?php head(); ?>

<form>
	<input type="text" name="search" />
	<input type="submit" name="submit" value="Search Types" />
</form>
<?php foreach( $types as $key => $type ): ?>
	<?php echo ($key+1); ?>) <?php echo $type->name; ?><br/>
<?php endforeach; ?>

<?php foot(); ?>