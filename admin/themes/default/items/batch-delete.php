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

    <section class="seven columns alpha">
    <fieldset id="item-list" class="panel">
        <h2 class="two columns alpha"><?php echo __('Items'); ?></h2>
        <div class="five columns omega">
            <ul>
            <?php 
            $itemCheckboxes = array();
            $excludedItems = false;
            foreach ($itemIds as $id) {
                if (!($item = get_record_by_id('item', $id))) {
                    continue;
                }
                
                if (is_allowed($item, 'delete')) {
                    $itemCheckboxes[$id] = metadata($item, array('Dublin Core', 'Title'));
                } else {
                    $excludedItems = true;
                }
                release_object($item);
            }
            echo '<li>' . $this->formMultiCheckbox('items[]', null, array('checked' => 'checked'), $itemCheckboxes, '</li><li>') . '</li>'; ?>
            </ul>
            <?php if ($excludedItems): ?>
            <p class="explanation"><?php echo __('Some items were excluded because you do not have permission to delete them.'); ?></p>
            <?php endif; ?>
            
            <p class="explanation"><?php echo __('Checked items will be deleted.'); ?></p>
    </fieldset>

    <input type="hidden" name="delete" value="1">
    
    </section>

    <section class="three columns omega">
        <div  id="save" class="panel">
            <input type="submit" class="big red button" value="<?php echo __('Delete Items'); ?>">
        </div>
    </section>

    <?php
    $hash = new Zend_Form_Element_Hash('batch_edit_hash');
    $hash->removeDecorator('Label');
    $hash->removeDecorator('HtmlTag');
    echo $hash;
    ?>
</form>
</div>
<?php if (!$isPartial): ?>
<?php echo foot(); ?>
<?php endif; ?>
