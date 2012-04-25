<?php echo flash(); ?>

<div class="seven columns alpha">

    <fieldset id="editcollection">
    
        <div class="element field">
            <label class="two columns alpha">Name</label>
            <div class="five columns omega inputs">
                <?php echo $this->formText('name', $collection->name, array('class'=>'textinput', 'size'=>'40')); ?>
                <?php echo form_error('name'); ?>
            </div>
        </div>
        
        <div class="element field">
            <label class="two columns alpha">Description</label>        
            <div class="five columns omega inputs">
                <?php echo form_error('description'); ?>
                <?php echo $this->formTextarea('description', $collection->description, array('class'=>'textinput', 'rows'=>'10', 'cols'=>'60')); ?>
            </div>
        </div>
        
        <div class="element field">
            <label class="two columns alpha">Collectors</label>
            <div class="five columns omega inputs">
                <?php echo $this->formTextarea('collectors', $collection->collectors, array('class' => 'texinput', 'rows' => '10', 'cols' => '50')); 
            ?>
                <p class="explanation"><?php echo __('List collectors for this collection (optional - enter one name per line)'); ?></p>
            </div>
        </div>
        
    </fieldset>
    
</div>


<?php fire_plugin_hook('admin_append_to_collections_form', $collection); ?>
