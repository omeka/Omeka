<?php head(); ?>

<h2>Item Types</h2>
<form>
	<input type="text" name="search" />
	<input type="submit" name="submit" value="Search Types" />
</form>
<?php foreach( $types as $type ): ?>
<div class="type">
	 <h3><a href="<?php echo uri('types/show/'.$type->id); ?>"><?php echo $type->name; ?></a></h3>
	<p><?php echo $type->description; ?></p>
</div>
<?php endforeach; ?>

<?php foot(); ?>