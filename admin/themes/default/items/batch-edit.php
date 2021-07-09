<?php
$title = __('Batch Edit Items');
if (!$isPartial):
    echo head(
        array(
            'title' => $title, 
            'bodyclass' => 'items batch-edit',
        )
    );
endif;
?>
<div title="<?php echo $title; ?>">

<form id="batch-edit-form" action="<?php echo html_escape(url('items/batch-edit-save')); ?>" method="post" accept-charset="utf-8">
    <section class="seven columns alpha">
        <fieldset id="item-list" class="panel">
            <h2><?php echo __('Items'); ?></h2>
            <ul>
            <?php 
            $itemCheckboxes = array();
            foreach ($itemIds as $id) {
                if (!($item = get_record_by_id('item', $id))) {
                    continue;
                }
    
                $showItemFields = true;
                if (!is_allowed($item, 'edit') || !is_allowed($item, 'delete')) {
                    $showItemFields = false;
                }
                $itemCheckboxes[$id] = metadata($item, 'display_title', array('no_escape' => true));
                release_object($item);
            }
            echo '<li>' . $this->formMultiCheckbox('items[]', null, array('checked' => 'checked'), $itemCheckboxes, '</li><li>') . '</li>';
            ?>
            </ul>
            <p class="explanation"><?php echo __('Changes will be applied to checked items.'); ?></p>
        </fieldset>

        <?php echo common('batch-edit-common', array(), 'items'); ?>

        <?php fire_plugin_hook('admin_items_batch_edit_form', array('view' => $this)); ?>
    
        <?php if ($showItemFields): ?>
        <fieldset>
            <h2><?php echo __('Delete Items'); ?></h2>
            <p class="explanation"><?php echo __('Check if you wish to delete selected items.'); ?></p>
            <div class="field">
                <label class="two columns alpha" for="delete"><?php echo __('Delete'); ?></label>
                <div class="inputs five columns omega">
                   <?php echo $this->formCheckbox('delete'); ?>
                </div>
            </div>
        </fieldset>
        <?php endif; ?>
    </section>

    <section class="three columns omega">
        <div id="save" class="panel">
            <input type="submit" class="big green button" value="<?php echo __('Save Changes'); ?>">
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
<script type="text/javascript">
    jQuery(document).ready(function(){
        var otherFormElements = jQuery('#item-fields select, #item-fields input');
        var elementsToEnable;
        jQuery('#delete').change(function() {
            if (this.checked) {
                elementsToEnable = otherFormElements.filter(':enabled');
                otherFormElements.prop('disabled', true);
            } else {
                elementsToEnable.prop('disabled', false);
            }
        });
        jQuery('input[name^="removeMetadata"]').change(function() {
            var name = this.name.replace('removeMetadata', 'metadata');
            jQuery('[name="' + name + '"]').prop('disabled', !!this.checked);
        });
    });
</script>
<?php
if (!$isPartial):
    echo foot();
endif;
