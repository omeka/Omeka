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
            <h2 class="two columns alpha"><?php echo __('Items'); ?></h2>
            <div class="five columns omega">
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
                    $itemCheckboxes[$id] = strip_formatting(metadata($item, array('Dublin Core', 'Title')));
                    release_object($item);
                }
                echo '<li>' . $this->formMultiCheckbox('items[]', null, array('checked' => 'checked'), $itemCheckboxes, '</li><li>') . '</li>'; ?>
                </ul>
                <p class="explanation"><?php echo __('Changes will be applied to checked items.'); ?></p>
            </div>
        </fieldset>
    
        <fieldset id="item-fields">
            <h2><?php echo __('Item Metadata'); ?></h2>
    
            <?php if ( is_allowed('Items', 'makePublic') ): ?>
        
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
    
            <?php if ( is_allowed('Items', 'makeFeatured') ): ?>
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
                    <?php echo $this->formText('metadata[tags]', null, array('size' => 32)); ?>
                    <p class="explanation"><?php echo __('List of tags to add to all checked items, separated by %s.', option('tag_delimiter')); ?></p>
                </div>
            </div>
        </fieldset>

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
<?php if (!$isPartial): ?>
<?php echo foot(); ?>
<?php endif; ?>
