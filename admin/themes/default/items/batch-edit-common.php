<fieldset id="item-fields">
    <h2><?php echo __('Item Metadata'); ?></h2>

    <?php if ( is_allowed('Items', 'makePublic') ): ?>
    <div class="field" role="group" aria-labelledby="metadata-public-label">
        <label class="two columns alpha" id="metadata-public-label"><?php echo __('Public?'); ?></label>
        <div class="inputs five columns omega">
            <?php
            $publicOptions = [''  => __('Select Below'),
                                   '1' => __('Public'),
                                   '0' => __('Not Public')
                                   ];
            echo $this->formSelect('metadata[public]', null, ['aria-labelledby' => 'metadata-public-label'], $publicOptions); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if ( is_allowed('Items', 'makeFeatured') ): ?>
    <div class="field" role="group" aria-labelledby="metadata-featured-label">
        <label class="two columns alpha" id="metadata-featured-label"><?php echo __('Featured?'); ?></label>
        <div class="inputs five columns omega">
            <?php
            $featuredOptions = [''  => __('Select Below'),
                                     '1' => __('Featured'),
                                     '0' => __('Not Featured')
                                     ];
            echo $this->formSelect('metadata[featured]', null, ['aria-labelledby' => 'metadata-featured-label'], $featuredOptions); ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="field" role="group" aria-labelledby="metadata-item_type_id-label">
        <label class="two columns alpha" id="metadata-item_type_id-label"><?php echo __('Item Type'); ?></label>
        <div class="inputs five columns omega">
            <?php
            $itemTypeOptions = get_db()->getTable('ItemType')->findPairsForSelectForm();
            $itemTypeOptions = ['' => __('Select Below')] + $itemTypeOptions;
            echo $this->formSelect('metadata[item_type_id]', null, ['aria-labelledby' => 'metadata-item_type_id-label'], $itemTypeOptions);
            ?>
            <label class="batch-edit-remove">
                <?php echo __('Remove?'); ?>
                <?php echo $this->formCheckbox('removeMetadata[item_type_id]'); ?>
            </label>
        </div>
    </div>

    <div class="field" role="group" aria-labelledby="metadata-collection_id-label">
        <label class="two columns alpha" id="metadata-collection_id-label"><?php echo __('Collection'); ?></label>
        <div class="inputs five columns omega">
            <?php
            $collectionOptions = get_db()->getTable('Collection')->findPairsForSelectForm();
            $collectionOptions = ['' => __('Select Below')] + $collectionOptions;
            echo $this->formSelect('metadata[collection_id]', null, ['aria-labelledby' => 'metadata-collection_id-label'], $collectionOptions);
            ?>
            <label class="batch-edit-remove">
                <?php echo __('Remove?'); ?>
                <?php echo $this->formCheckbox('removeMetadata[collection_id]'); ?>
            </label>
        </div>
    </div>

    <div class="field" role="group" aria-labelledby="metadata-tags-label">
        <label class="two columns alpha" id="metadata-tags-label"><?php echo __('Add Tags'); ?></label>
        <div class="inputs five columns omega">
            <p class="explanation" id="add-tags-explanation"><?php echo __('List of tags to add to all checked items, separated by %s.', option('tag_delimiter')); ?></p>
            <?php echo $this->formText('metadata[tags]', null, ['size' => 32, 'aria-labelledby' => 'metadata-tags-label', 'aria-describedby' => 'add-tags-explanation']); ?>
        </div>
    </div>
</fieldset>
