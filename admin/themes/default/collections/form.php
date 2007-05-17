<?php echo flash(); ?>
<fieldset>
<div class="field">
<?php text(array('name'=>'name', 'class'=>'textinput', 'id'=>'name'),$collection->name, 'Collection Name'); ?>
</div>

<div class="field">
<?php textarea(array('name'=>'description', 'class'=>'textinput', 'id'=>'description','rows'=>'10'),$collection->description, 'Collection Description'); ?>
</div>

<div class="field">
<label for="collector">Collection Collector</label><input type="text" name="collector" id="collector" class="textinput" value="<?php echo $collection->collector; ?>" />	
</div>

<div class="field">
<?php 
	radio(array('name'=>'active'),array('0'=>'Inactive','1'=>'Active'), $collection->active); ?>
</div>

<div class="field">	
<?php 
	radio(array('name'=>'featured'),array('0'=>'Not Featured','1'=>'Featured'), $collection->featured); ?>
</div>	

</fieldset>