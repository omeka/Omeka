<form action="<?php echo url_for('item-types/add-element'); ?>" method="post" accept-charset="utf-8" id="add-element-form">

<h2>Add an Element to this Item Type</h2>

<fieldset>
    <legend>Choose an existing element</legend>
    <div class="field">
    <?php echo select_item_type_elements(array('name'=>'element-id')); ?>
    </div>
</fieldset>

<fieldset>
    <legend>Or Create a new Element</legend>
    <div class="field">
    <?php   
        echo __v()->formLabel('element-name', 'Name');
        echo __v()->formText('element-name', null, array('class'=>'textinput')); 
    ?>   
    </div>
    
    <div class="field">
    <?php   
        echo __v()->formLabel('element-description', 'Description');
        echo __v()->formTextarea('element-description', null, array('class'=>'textinput', 'cols'=>80, 'rows'=>10)); 
    ?>   
    </div> 
    
    <div class="field" id="select-data-type">
        <?php echo __v()->formLabel('element-data-type-id', ) ?>
        <?php echo __v()->formRadio('element-data-type-id', 1, array('label_class'=>'radiolabel', 'label_style'=>'float:left;'), $datatypes, ''); ?>
    </div>
    
    <?php echo __v()->formHidden('item-type-id', $itemtype->id); ?>
</fieldset>

<fieldset>
    <p><?php echo __v()->formSubmit('element-form-submit', 'Add This Element'); ?></p>
</fieldset>

</form>
