<div id="tag-form" class="field">
    <?php
        $tags = $item->getTags();
    ?>
    <input type="hidden" name="tags-to-add" id="tags-to-add" value="" />
    <input type="hidden" name="tags-to-delete" id="tags-to-delete" value="" />
    <div id="add-tags">
        <label><?php echo __('Add Tags'); ?></label>           
        <input type="text" name="tags" size="20" id="tags" class="textinput" value="" />
        <p id="add-tags-explanation" class="explanation"><?php echo __('Separate tags with %s', option('tag_delimiter')); ?></p>
        <input type="submit" name="add-tags-button" id="add-tags-button" class="button" value="<?php echo __('Add Tags'); ?>" />
        <div id="tags-sr-alerts" class="sr-only" aria-live="polite" 
            data-duplicate-alert="<?php echo __('Tag already added.'); ?>"
            data-success-alert="<?php echo __('Tag added.'); ?>">
        </div>
        <div id="tags-sr-alerts-repeat" class="sr-only" aria-live="polite"></div>
    </div>
    <div id="all-tags">
    <?php if ($tags): ?>
        <h3><?php echo __('All Tags'); ?></h3>
        
        <ul id="all-tags-list" class="tag-list" data-new-tag-id="0">
            <?php foreach( $tags as $tag ): ?>
                <?php $tagId = $tag->id; ?>
                <li>
                    <div role="group" aria-labelledby="tag-<?php echo $tagId; ?>-name" class="tag-group">
                        <span class="tag" id="tag-<?php echo $tagId; ?>-name"><?php echo html_escape($tag->name); ?></span>
                        <button type="button" class="has-tooltip remove-tag button" aria-labelledby="tag-<?php echo $tagId; ?>-remove-tooltip">
                            <span class="icon" aria-hidden="true"></span>
                            <div id="tag-<?php echo $tagId; ?>-remove-tooltip" class="tooltip" popover="auto"><?php echo __('Remove'); ?></div>
                        </button>
                        <button type="button" class="has-tooltip undo-remove-tag button" aria-labelledby="tag-<?php echo $tagId; ?>-undo-tooltip">
                            <span class="icon" aria-hidden="true"></span>
                            <div id="tag-<?php echo $tagId; ?>-undo-tooltip" class="tooltip" popover="auto"><?php echo __('Undo remove'); ?></div>
                        </button>
                    </div>  
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    </div>
</div>
<?php fire_plugin_hook('admin_items_form_tags', ['item' => $item, 'view' => $this]); ?>
