<?php echo flash(); ?>

<form action="<?php echo html_escape(uri('item-types/add-element')); ?>" method="post" accept-charset="utf-8" id="add-element-form">

<h3>Add an Element to this Item Type</h3>

<div class="field" id="existing-element">
    <label for="element-id">Choose an existing element</label>
    <div class="inputs">
    <?php echo select_item_type_elements(array('name'=>'element-id')); ?>
    </div>
</div>

<div id="new-element">
<div class="field">
    <label>Or Create a new Element</label>
    <div class="inputs">
    <?php   
       /// echo __v()->formLabel('element-name', 'Name');
        echo __v()->formText('element-name', null, array('class'=>'textinput')); 
    ?>   
    </div>
 </div>
    <div class="field">
    <?php   
        echo __v()->formLabel('element-description', 'Description'); ?>
<div class="inputs">
<?php
        echo __v()->formTextarea('element-description', null, array('class'=>'textinput', 'cols'=>40, 'rows'=>10)); 
    ?>   
    </div> 
</div>
    <div class="field" id="select-data-type">
        <?php echo __v()->formLabel('element-data-type-id', 'Data Type'); ?>
<div class="inputs">
        <?php echo __v()->formRadio('element-data-type-id', 1, array('label_class'=>'radiolabel', 'label_style'=>'float:left;'), $datatypes, ''); ?>
    </div>
    </div>
</div>
    <?php echo __v()->formHidden('item-type-id', $itemtype->id); ?>
</fieldset>

<?php echo __v()->formSubmit('element-form-submit', 'Add Element', array('class'=>'submit submit-medium')); ?>


</form>
