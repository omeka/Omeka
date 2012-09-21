<?php
$title = __('Batch Delete Items');
if (!$isPartial):
    echo head(array('title' => $title, 
               'bodyclass' => 'advanced-search', 
               'bodyid' => 'advanced-search-page'));
?>

<?php endif; ?>
        
<div title="<?php echo $title; ?>">

<form id="batch-edit-form" action="<?php echo html_escape(url('items/batch-edit-save')); ?>" method="post" accept-charset="utf-8">

    <div id="save" class="three columns omega panel">
        <input type="submit" class="big green button" value="<?php echo __('Delete Items'); ?>">
    </div>

    <fieldset id="item-list" class="panel">
        <h2 class="two columns alpha"><?php echo __('Items'); ?></h2>
        <div class="four columns omega">
            <div>
            <?php 
            $itemCheckboxes = array();
            $excludedItems = false;
            foreach ($itemIds as $id) {
                if (!($item = get_record_by_id('item', $id))) {
                    continue;
                }
                
                if (has_permission($item, 'delete')) {
                    $itemCheckboxes[$id] = metadata($item, array('Dublin Core', 'Title'));
                } else {
                    $excludedItems = true;
                }
                release_object($item);
            }
            echo $this->formMultiCheckbox('items[]', null, array('checked' => 'checked'), $itemCheckboxes);
            ?>
            </div>
            <?php if ($excludedItems): ?>
            <p class="explanation"><?php echo __('Some items were excluded because you do not have permission to delete them.'); ?></p>
            <?php endif; ?>
            
            <p class="explanation"><?php echo __('Checked items will be deleted.'); ?></p>
        </div>
    </fieldset>

    <input type="hidden" name="delete" value="1">

    <?php
    $hash = new Zend_Form_Element_Hash('batch_edit_hash');
    $hash->removeDecorator('Label');
    $hash->removeDecorator('HtmlTag');
    echo $hash;
    ?>
</form>
<?php if (!$isPartial): ?>
<?php echo foot(); ?>
<?php endif; ?>
