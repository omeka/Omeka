<?php echo flash(); ?>

<fieldset id="editcollection">
	<legend>Collection Details</legend>

<div class="field">
<?php text(array('name'=>'name', 'class'=>'textinput', 'id'=>'name'),$collection->name, 'Collection Name'); ?>
</div>

<div class="field">
<?php textarea(array('name'=>'description', 'class'=>'textinput', 'id'=>'description','rows'=>'10'),$collection->description, 'Collection Description'); ?>
</div>

<?php $entities = entities(); ?>

<?php foreach( $collection->Collectors as $k => $collector ): ?>
<h3>Collectors: </h3>
<ul id="collectors">
	<li><?php echo h($collector->getName()); ?></li>
</ul>
<?php endforeach; ?>

<div class="field">
<?php 
	select(array('name'=>'collectors[]', 'id'=>'collector'), $entities, null, 'Add a Collector (optional)', 'id', 'name'); 
?>
</div>

<h3>Status: </h3>
<div class="field">
	<label for="public">Public</label>	
<?php 
	// radio(array('name'=>'public'),array('0'=>'Not Public','1'=>'Public'), $collection->public);
	checkbox(array('name'=>'public'), $collection->public); ?>
</div>

<div class="field">
	<label for="featured">Featured</label>	
<?php 
	//radio(array('name'=>'featured'),array('0'=>'Not Featured','1'=>'Featured'), $collection->featured); 
	checkbox(array('name'=>'featured'), $collection->featured); ?>
</div>	

</fieldset>