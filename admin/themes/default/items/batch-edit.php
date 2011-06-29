<?php
$title = 'Batch Edit Items';
if (!$isPartial):
    head(array('title' => $title, 
               'bodyclass' => 'advanced-search', 
               'bodyid' => 'advanced-search-page'));
?>
<h1><?php echo $title; ?></h1>
<div id="primary">
<?php endif; ?>
<div title="<?php echo $title; ?>">
<form id="batch-edit-form" action="<?php echo html_escape(uri('items/batch-edit-save')); ?>" method="post" accept-charset="utf-8">
    <fieldset id="item-list" style="float:right; width: 28%;">
        <legend>Items</legend>
        <p><em>Changes will be applied to checked items.</em></p>
        <div style="height: 250px; overflow-y: auto; overflow-x:hidden;border: 1px solid #ddd; padding: 10px;">
        <?php 
        $itemCheckboxes = array();
        foreach ($itemIds as $id) {
            $item = get_item_by_id($id);
            $showItemFields = true;
            if (!has_permission($item, 'edit') || !has_permission($item, 'delete')) {
                $showItemFields = false;
            }
            $itemCheckboxes[$id] = item('Dublin Core', 'Title', null, $item);
            release_object($item);
        }
        echo $this->formMultiCheckbox('items[]', null, array('checked' => 'checked'), $itemCheckboxes); ?>
        </div>
    </fieldset>
    
    <fieldset id="item-fields" style="width: 70%; margin-bottom:2em;">
        <legend>Item Metadata</legend>
        <?php if ( has_permission('Items', 'makePublic') ): ?>
        <div class="field">
        <label for="metadata[public]">Public?</label>
        <?php
        $publicOptions = array(''  => 'Select Below',
                               '1' => 'Public',
                               '0' => 'Not Public'
                               );
        echo $this->formSelect('metadata[public]', null, array(), $publicOptions); ?>
        </div>
        <?php endif; ?>

        <?php if ( has_permission('Items', 'makeFeatured') ): ?>
        <div class="field">
        <label for="metadata[featured]">Featured?</label>
        <?php
        $featuredOptions = array(''  => 'Select Below',
                                 '1' => 'Featured',
                                 '0' => 'Not Featured'
                                 );
        echo $this->formSelect('metadata[featured]', null, array(), $featuredOptions); ?>
        </div>
        <?php endif; ?>
        
        <div class="field">
        <label for="metadata[item_type_id]">Item Type</label>
        <?php
        $itemTypeOptions = get_db()->getTable('ItemType')->findPairsForSelectForm();
        $itemTypeOptions = array('' => 'Select Below ') + $itemTypeOptions;
        echo $this->formSelect('metadata[item_type_id]', null, array(), $itemTypeOptions);
        ?>
        <div class="batch-edit-remove">
        <?php echo $this->formCheckbox('removeMetadata[item_type_id]'); ?>
        <label for="removeMetadata[item_type_id]" style="float:none;">Remove?</label>
        </div>
        </div>
        
        <div class="field">
        <label for="metadata[collection_id]">Collection</label>
        <?php
        $collectionOptions = get_db()->getTable('Collection')->findPairsForSelectForm();
        $collectionOptions = array('' => 'Select Below ') + $collectionOptions;
        echo $this->formSelect('metadata[collection_id]', null, array(), $collectionOptions);
        ?>
        <div class="batch-edit-remove">
        <?php echo $this->formCheckbox('removeMetadata[collection_id]'); ?>
        <label for="removeMetadata[collection_id]" style="float:none;">Remove?</label>
        </div>
        </div>

        <div class="field">
            <label for="metadata[tags]">Add Tags</label>
            <?php echo $this->formText('metadata[tags]', null, array('size' => 32, 'class' => 'textinput')); ?>
            <p class="explanation">List of tags to add to all checked items, separated by <?php echo settings('tag_delimiter'); ?>.</p>
        </div>
    </fieldset>

    <?php fire_plugin_hook('admin_append_to_items_batch_edit_form'); ?>

    <?php if ($showItemFields): ?>
    <fieldset style="width: 70%;">
        <legend>Delete Items</legend>
        <p class="explanation">Check if you wish to delete selected items.</p>
        <div class="field">
            <label for="delete">Delete</label>
            <?php echo $this->formCheckbox('delete'); ?>
        </div>
    </fieldset>
    <?php endif; ?>

    <?php
    $hash = new Zend_Form_Element_Hash('batch_edit_hash');
    $hash->removeDecorator('Label');
    $hash->removeDecorator('HtmlTag');
    echo $hash;
    ?>
    <input type="submit" value="Save Changes">
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
<?php if (!$isPartial): ?>
</div>
<?php foot(); ?>
<?php endif; ?>
