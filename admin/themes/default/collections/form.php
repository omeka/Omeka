<?php echo flash(); ?>

<div class="seven columns alpha">

    <fieldset id="editcollection">
    
        <div class="element">
            <h3 class="two columns alpha">Name</h3>
            <div class="five columns omega inputs">
                <?php echo text(array('name'=>'name', 'class'=>'textinput', 'id'=>'name', 'size'=>'40'),$collection->name); ?>
                <?php echo form_error('name'); ?>
            </div>
        </div>
        
        <div class="element">
            <h3 class="two columns alpha">Description</h3>        
            <div class="five columns omega inputs">
                <?php echo form_error('description'); ?>
                <?php echo textarea(array('name'=>'description', 'class'=>'textinput', 'id'=>'description','rows'=>'10'),$collection->description); ?>
            </div>
        </div>
        
        <div class="element">
            <h3 class="two columns alpha">Collectors</h2>
            <div class="five columns omega inputs">
                <?php echo $this->formTextarea('collectors', $collection->collectors, array('class' => 'texinput', 'rows' => '10', 'cols' => '50')); 
            ?>
                <?php echo $this->formLabel('collectors', __('List collectors for this collection (optional - enter one name per line)')); ?>
            </div>
        </div>
        
    </fieldset>

</div>

<?php fire_plugin_hook('admin_append_to_collections_form', $collection); ?>
