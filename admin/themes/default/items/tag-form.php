<div class="field">
    <div id="tag-form">
        <?php
            $tags = $item->getTags();
        ?>
        <input type="hidden" name="tags-to-add" size="30" id="tags-to-add" value="" />
        <input type="hidden" name="tags-to-delete" size="30" id="tags-to-delete" value="" />
        <div id="add-tags">
            <h3><?php echo __('Add Tags'); ?></h3>           
            <input type="text" name="tags" size="20" id="tags" class="textinput" value="" />
            <input type="button" name="add-tags-button" id="add-tags-button" value="<?php echo __('Add Tags'); ?>" />
            <p id="add-tags-explanation"><?php echo __('Separate tags with %s', settings('tag_delimiter')); ?></p>
        </div>
        <div id="all-tags">
        <?php if ($tags): ?>
            <h3><?php echo __('All Tags'); ?></h3>
            <ul id="all-tags-list">
                <?php foreach( $tags as $tag ): ?>
                    <li>
                        <?php echo __v()->formImage('undo-remove-tag-' . $tag->id, 
                                                    $tag->name,
                                                    array(
                                                        'src'   => img('silk-icons/add.png'),
                                                        'class' => 'undo_remove_tag')); 
                              echo __v()->formImage('remove-tag-' . $tag->id,
                                                    $tag->name,
                                                    array(
                                                        'src'   => img('silk-icons/delete.png'),
                                                        'class' => 'remove_tag'));
                              echo $tag->name; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        </div>
    </div>
<?php fire_plugin_hook('admin_append_to_items_form_tags', $item); ?>
</div>
