<?php echo flash(); ?>
<fieldset>
<div class="field">
<?php text(array('name'=>'name', 'class'=>'textinput', 'id'=>'name'),$collection->name, 'Collection Name'); ?>
</div>

<div class="field">
<?php textarea(array('name'=>'description', 'class'=>'textinput', 'id'=>'description','rows'=>'10'),$collection->description, 'Collection Description'); ?>
</div>

<?php 
	$entities = entities();
?>

<h3>Collectors</h3>
<ul id="collectors">
<?php foreach( $collection->Collectors as $k => $collector ): ?>
	<li><?php echo $collector->getName(); ?></li>
<?php endforeach; ?>
</ul>

<?php 
	select(array('name'=>'collectors[]', 'id'=>'collector'), $entities, null, 'Add a Collector (optional)', 'id', 'name'); 
?>

<div class="field">
<?php 
	radio(array('name'=>'public'),array('0'=>'Not Public','1'=>'Public'), $collection->public); ?>
</div>

<div class="field">	
<?php 
	radio(array('name'=>'featured'),array('0'=>'Not Featured','1'=>'Featured'), $collection->featured); ?>
</div>	

</fieldset>