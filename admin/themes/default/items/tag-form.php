<div class="field">
    <div id="tag-form">
        <?php
            $myTags = $item->entityTags(current_user());
            $otherTags = $item->getTags();
        ?>
        <input type="hidden" name="my-tags-to-add" size="30" id="my-tags-to-add" value="" />
        <input type="hidden" name="my-tags-to-delete" size="30" id="my-tags-to-delete" value="" />
        <input type="hidden" name="other-tags-to-delete" size="30" id="other-tags-to-delete" value="" />
        <div id="add-tags">
            <h3>Add Tags</h3>           
            <input type="text" name="tags" size="20" id="tags" class="textinput" value="" />
            <div id="tag-choices" class="autocomplete"></div>
            <input type="button" name="add-tags-button" id="add-tags-button" value="Add Tags" />
            <p id="add-tags-explanation">Separate tags with commas.</p>
        </div>
        <div id="my-tags">
            <h3>My Tags</h3>
            <ul id="my-tags-list">
                <?php foreach( $myTags as $myTag ): ?>
                <li class="tag-delete">
                    <?php echo __v()->formImage('undo-remove-tag-' . $myTag->id, 
                                                $myTag->name,
                                                array(
                                                    'src'   => img('add.png'),
                                                    'class' => 'undo_remove_tag')); 
                          echo __v()->formImage('remove-tag-' . $myTag->id,
                                                $myTag->name,
                                                array(
                                                    'src'   => img('delete.gif'),
                                                    'class' => 'remove_tag')); 
                          echo html_escape($myTag->name); ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php if (!empty($otherTags)): ?>
            <div id="other-tags">
                <h3>All Tags</h3>
                <ul id="other-tags-list">
                    <?php foreach( $otherTags as $otherTag ): ?>
                        <li>
                            <?php if (has_permission('Items','untagOthers')): ?>
                                <?php echo __v()->formImage('undo-remove-tag-' . $otherTag->id, 
                                                            $otherTag->name,
                                                            array(
                                                                'src'   => img('add.png'),
                                                                'class' => 'undo_remove_tag')); 
                                      echo __v()->formImage('remove-tag-' . $otherTag->id,
                                                            $otherTag->name,
                                                            array(
                                                                'src'   => img('delete.gif'),
                                                                'class' => 'remove_tag')); ?>
                            <?php endif; ?>
                            <?php echo html_escape($otherTag->name); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
<?php fire_plugin_hook('admin_append_to_items_form_tags', $item); ?>
</div>