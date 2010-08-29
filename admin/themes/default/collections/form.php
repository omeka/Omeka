<?php echo flash(); ?>

<fieldset id="editcollection">
    <h2>Collection Details <span id="required-note">* Required Fields</span></h2>

<div class="field">
    <?php echo $this->formLabel('name', 'Collection Name', array('class' => 'required')); ?>
    <div class="inputs">
        <?php echo text(array('name'=>'name', 'class'=>'textinput', 'id'=>'name', 'size'=>'40'),$collection->name); ?>
    </div>
<?php echo form_error('name'); ?>
</div>

<div class="field">
    <?php echo $this->formLabel('description', 'Collection Description'); ?>
    
<?php echo form_error('description'); ?>
<div class="inputs">
<?php echo textarea(array('name'=>'description', 'class'=>'textinput', 'id'=>'description','rows'=>'10','cols'=>'60'),$collection->description); ?>
</div>
</div>

<h2>Collectors</h2>
<div class="field">
    <?php echo $this->formLabel('collectors', 'List collectors for this collection (optional - enter one name per line)'); ?>
    <div class="inputs">
        <div class="input">
    <?php echo $this->formTextarea('collectors', $collection->collectors, array('class' => 'texinput', 'rows' => '10', 'cols' => '60')); 
?>
</div></div>
</div>

<h2>Status: </h2>
<div class="field">
    <?php echo $this->formLabel('public', 'Public'); ?>
<?php 
    echo radio(array('name'=>'public'),array('0'=>'Not Public','1'=>'Public'), $collection->isPublic());
?>
</div>

<div class="field">
    <?php echo $this->formLabel('featured', 'Featured'); ?>
<?php 
    echo radio(array('name'=>'featured'),array('0'=>'Not Featured','1'=>'Featured'), $collection->isFeatured()); 
?>
</div>  

</fieldset>

<?php fire_plugin_hook('admin_append_to_collections_form', $collection); ?>