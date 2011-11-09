<?php echo flash(); ?>

<fieldset id="editcollection">
    <h2><?php echo __('Collection Details'); ?> <span id="required-note">* <?php echo __('Required Fields'); ?></span></h2>

<div class="field">
    <?php echo $this->formLabel('name', __('Collection Name'), array('class' => 'required')); ?>
    <div class="inputs">
        <?php echo text(array('name'=>'name', 'class'=>'textinput', 'id'=>'name', 'size'=>'40'),$collection->name); ?>
    </div>
<?php echo form_error('name'); ?>
</div>

<div class="field">
    <?php echo $this->formLabel('description', __('Collection Description')); ?>
    
<?php echo form_error('description'); ?>
<div class="inputs">
<?php echo textarea(array('name'=>'description', 'class'=>'textinput', 'id'=>'description','rows'=>'10','cols'=>'60'),$collection->description); ?>
</div>
</div>

<h2>Collectors</h2>
<div class="field">
    <?php echo $this->formLabel('collectors', __('List collectors for this collection (optional - enter one name per line)')); ?>
    <div class="inputs">
        <div class="input">
    <?php echo $this->formTextarea('collectors', $collection->collectors, array('class' => 'texinput', 'rows' => '10', 'cols' => '60')); 
?>
</div></div>
</div>

<h2>Status: </h2>
<div class="field">
    <?php echo $this->formLabel('public', __('Public')); ?>
<?php 
    echo radio(array('name'=>'public'),array('0'=>__('Not Public'),'1'=>__('Public')), $collection->isPublic());
?>
</div>

<div class="field">
    <?php echo $this->formLabel('featured', __('Featured')); ?>
<?php 
    echo radio(array('name'=>'featured'),array('0'=>__('Not Featured'),'1'=>__('Featured')), $collection->isFeatured()); 
?>
</div>  

</fieldset>

<?php fire_plugin_hook('admin_append_to_collections_form', $collection); ?>