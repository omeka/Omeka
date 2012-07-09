<?php
$title = __('Batch Edit Items');
if (!$isPartial):
    head(array('title' => $title, 
               'bodyclass' => 'advanced-search', 
               'bodyid' => 'advanced-search-page'));
?>

<?php endif; ?>
        
<div title="<?php echo $title; ?>">

<form id="batch-edit-form" action="<?php echo html_escape(uri('items/batch-edit-save')); ?>" method="post" accept-charset="utf-8">

    <div id="save" class="three columns omega panel">
        <input type="submit" class="big green button" value="<?php echo __('Save Changes'); ?>">
    </div>

        <fieldset id="item-list" class="panel">
            <h2 class="two columns alpha"><?php echo __('Items'); ?></h2>
            <div class="four columns omega">
                <div>
                <?php 
                $itemCheckboxes = array();
                foreach ($itemIds as $id) {
                    if (!($item = get_item_by_id($id))) {
                        continue;
                    }
        
                    $showItemFields = true;
                    if (!has_permission($item, 'edit') || !has_permission($item, 'delete')) {
                        $showItemFields = false;
                    }
                    $itemCheckboxes[$id] = item(array('Dublin Core', 'Title'), null, $item);
                    release_object($item);
                }
                echo $this->formMultiCheckbox('items[]', null, array('checked' => 'checked'), $itemCheckboxes); ?>
                </div>
                <p class="explanation"><?php echo __('Changes will be applied to checked items.'); ?></p>
                
            </div>
        </fieldset>
    
    <fieldset id="item-fields">
        <h2><?php echo __('Item Metadata'); ?></h2>

        <?php if ( has_permission('Items', 'makePublic') ): ?>
    
        <div class="field">
            <label class="two columns alpha" for="metadata[public]"><?php echo __('Public?'); ?></label>
            <div class="inputs five columns omega">
                <?php
                $publicOptions = array(''  => __('Select Below'),
                                       '1' => __('Public'),
                                       '0' => __('Not Public')
                                       );
                echo $this->formSelect('metadata[public]', null, array(), $publicOptions); ?>
            </div>
        </div>
    
        <?php endif; ?>

        <?php if ( has_permission('Items', 'makeFeatured') ): ?>
        <div class="field">
            <label class="two columns alpha" for="metadata[featured]"><?php echo __('Featured?'); ?></label>
            <div class="inputs five columns omega">
                <?php
                $featuredOptions = array(''  => __('Select Below'),
                                         '1' => __('Featured'),
                                         '0' => __('Not Featured')
                                         );
                echo $this->formSelect('metadata[featured]', null, array(), $featuredOptions); ?>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="field">
            <label class="two columns alpha" for="metadata[item_type_id]"><?php echo __('Item Type'); ?></label>
            <div class="inputs five columns omega">
            <?php
            $itemTypeOptions = get_db()->getTable('ItemType')->findPairsForSelectForm();
            $itemTypeOptions = array('' => __('Select Below')) + $itemTypeOptions;
            echo $this->formSelect('metadata[item_type_id]', null, array(), $itemTypeOptions);
            ?>
                <div class="batch-edit-remove">
                <?php echo $this->formCheckbox('removeMetadata[item_type_id]'); ?>
                <label for="removeMetadata[item_type_id]" style="float:none;"><?php echo __('Remove?'); ?></label>
                </div>
            </div>
        </div>
        
        <div class="field">
            <label class="two columns alpha" for="metadata[collection_id]"><?php echo __('Collection'); ?></label>
            <div class="inputs five columns omega">
                <?php
                $collectionOptions = get_db()->getTable('Collection')->findPairsForSelectForm();
                $collectionOptions = array('' => __('Select Below')) + $collectionOptions;
                echo $this->formSelect('metadata[collection_id]', null, array(), $collectionOptions);
                ?>
                <div class="batch-edit-remove">
                    <?php echo $this->formCheckbox('removeMetadata[collection_id]'); ?>
                    <label class="two columns alpha" for="removeMetadata[collection_id]" style="float:none;"><?php echo __('Remove?'); ?></label>
                </div>
            </div>
        </div>

        <div class="field">
            <label class="two columns alpha" for="metadata[tags]"><?php echo __('Add Tags'); ?></label>
            <div class="inputs five columns omega">
                <?php echo $this->formText('metadata[tags]', null, array('size' => 32, 'class' => 'textinput')); ?>
                <p class="explanation"><?php echo __('List of tags to add to all checked items, separated by %s.', settings('tag_delimiter')); ?></p>
            </div>
        </div>
    </fieldset>

    <?php fire_plugin_hook('admin_append_to_items_batch_edit_form'); ?>

    <?php if ($showItemFields): ?>
    <fieldset style="width: 70%;">
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

    <?php
    $hash = new Zend_Form_Element_Hash('batch_edit_hash');
    $hash->removeDecorator('Label');
    $hash->removeDecorator('HtmlTag');
    echo $hash;
    ?>
   
</form>

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
<?php foot(); ?>
<?php endif; ?>
