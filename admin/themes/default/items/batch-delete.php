<?php
$title = __('Batch Delete Items');
if (!$isPartial):
    echo head([
        'title' => $title,
        'bodyclass' => 'items batch-edit',
    ]);
endif;
?>
<div title="<?php echo $title; ?>">

<form id="batch-edit-form" action="<?php echo html_escape(url('items/batch-edit-save')); ?>" method="post" accept-charset="utf-8">
    <section class="seven columns alpha">
        <fieldset id="item-list" class="panel">
            <h2><?php echo __('Items'); ?></h2>
            <div class="inputs">
                <?php
                    $itemCheckboxes = [];
                    $excludedItems = false;
                ?>
                <p id="item-list-explanation" class="explanation"><?php echo __('Checked items will be deleted.'); ?></p>
                <?php if ($excludedItems): ?>
                <p id="exluded-items-explanation" class="explanation"><?php echo __('Some items were excluded because you do not have permission to delete them.'); ?></p>
                <?php endif; ?>
                <ul>
                <?php
                foreach ($itemIds as $id) {
                    if (!($item = get_record_by_id('item', $id))) {
                        continue;
                    }

                    if (is_allowed($item, 'delete')) {
                        $itemCheckboxes[$id] = metadata($item, 'display_title', ['no_escape' => true]);
                    } else {
                        $excludedItems = true;
                    }
                    release_object($item);
                }
                echo '<li>' . $this->formMultiCheckbox('items[]', null, ['checked' => 'checked', 'aria-describedby' => 'item-list-explanation'], $itemCheckboxes, '</li><li>') . '</li>'; ?>
                </ul>

            </div>
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
<?php
if (!$isPartial):
    echo foot();
endif;
