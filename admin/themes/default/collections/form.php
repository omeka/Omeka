<?php echo flash(); ?>

<div class="seven columns alpha">

    <div class="field two columns alpha">
        <?php echo $this->formLabel('name', __('Name'), array('class' => 'required')); ?>
    </div>
    
    <div class="inputs five columns omega">
        <?php echo $this->formText('name', $collection->name, array('class'=>'textinput', 'size'=>'40')); ?>
    </div>
    
    <div class="field two columns alpha">
        <?php echo $this->formLabel('description', __('Description')); ?>
    </div>
        
    <div class="inputs five columns omega">
    <?php echo $this->formTextarea('description', $collection->description, array('class'=>'textinput', 'rows'=>'10', 'cols'=>'60')); ?>
    </div>
    
    <div class="field two columns alpha">
        <?php echo $this->formLabel('collectors', __('Collectors')); ?>
    </div>
    <div class="inputs five columns omega">
        <?php echo $this->formTextarea('collectors', $collection->collectors, array('class' => 'texinput', 'rows' => '10', 'cols' => '60')); 
    ?>
    </div>
    
</div>


<?php fire_plugin_hook('admin_append_to_collections_form', $collection); ?>
