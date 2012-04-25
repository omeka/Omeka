<?php echo flash(); ?>

<fieldset id="editcollection">
    <h2><?php echo __('Collection Details'); ?> <span id="required-note">* <?php echo __('Required Fields'); ?></span></h2>

<div class="field">
    <?php echo $this->formLabel('name', __('Collection Name'), array('class' => 'required')); ?>
    <div class="inputs">
        <?php echo $this->formText('name', $collection->name, array('class'=>'textinput', 'size'=>'40')); ?>
    </div>
</div>

<div class="field">
    <?php echo $this->formLabel('description', __('Collection Description')); ?>
    
<div class="inputs">
<?php echo $this->formTextarea('description', $collection->description, array('class'=>'textinput', 'rows'=>'10', 'cols'=>'60')); ?>
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
    echo $this->formCheckbox('public', $collection->public, array(), array('1', '0'));
?>
</div>

<div class="field">
    <?php echo $this->formLabel('featured', __('Featured')); ?>
<?php 
    echo $this->formCheckbox('featured', $collection->featured, array(), array('1', '0')); 
?>
</div>  

</fieldset>

<?php fire_plugin_hook('admin_append_to_collections_form', $collection); ?>
