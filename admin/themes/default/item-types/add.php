<?php 
$pageTitle = __('Add Item Type');
echo head(array('title'=>$pageTitle,'bodyclass'=>'item-types')); ?>

<div id="primary">
    <form method="post" action="">
        <?php include 'form.php';?>
        <div id="save" class="three columns omega panel">
            <?php echo $form->getElement(Omeka_Form_ItemTypes::SUBMIT_ADD_ELEMENT_ID); ?>
        </div>
    </form>
</div>
<?php echo foot(); ?>
