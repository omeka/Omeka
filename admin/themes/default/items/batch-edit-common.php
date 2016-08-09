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
