<?php
    $itemTitle = strip_formatting(item('Dublin Core', 'Title'));
    if ($itemTitle != '' && $itemTitle != __('[Untitled]')) {
        $itemTitle = ': &quot;' . $itemTitle . '&quot; ';
    } else {
        $itemTitle = '';
    }
    $itemTitle = __('Edit Item #%s', item('id')) . $itemTitle;
?>
<?php head(array('title'=> $itemTitle, 'bodyclass'=>'items primary','content_class' => 'vertical-nav'));?>

        <?php include 'form-tabs.php'; // Definitions for all the tabs for the form. ?>
        
            <form method="post" enctype="multipart/form-data" id="item-form" action="">
                <?php include 'form.php'; ?>
                
                <div id="save" class="three columns omega panel">
                    
                    <?php echo $this->formSubmit('submit', __('Save Changes'), array('id'=>'save-changes', 'class'=>'submit big green button')); ?>
                    <a href="<?php echo html_escape(public_uri('items/show/'.item('id'))); ?>" class="big blue button" target="_blank">View Public Page</a>
                    <?php echo link_to_item(__('Delete Item'), array('class' => 'big red button'), 'delete-confirm'); ?>
        
                    <div id="public-featured">
                        <?php if ( has_permission('Items', 'makePublic') ): ?>
                                <label for="public"><?php echo __('Public'); ?>:</label> 
                                <?php echo $this->formCheckbox('public', $item->public, array(), array('1', '0')); ?>
                        <?php endif; ?>
                        <?php if ( has_permission('Items', 'makeFeatured') ): ?>
                                <label for="featured"><?php echo __('Featured'); ?>:</label> 
                                <?php echo $this->formCheckbox('featured', $item->featured, array(), array('1', '0')); ?>
                        <?php endif; ?>
                    </div>
                    
                        <div id="collection-form" class="field">
                            <?php echo $this->formLabel('collection-id', __('Collection'));?>
                            <div class="inputs">
                                <?php
                                    echo $this->formSelect(
                                        'collection_id',
                                        $item->collection_id,
                                        array('id' => 'collection-id'),
                                        get_table_options('Collection')
                                    );
                                ?>
                            </div>
                        </div>
                        
                        <div id="tag-form" class="field">
                            <?php $tags = $item->getTags(); ?>
                            <input type="hidden" name="tags-to-add" size="30" id="tags-to-add" value="" />
                            <input type="hidden" name="tags-to-delete" size="30" id="tags-to-delete" value="" />
                            <div id="add-tags">
                                <label><?php echo __('Add Tags'); ?></label>           
                                <input type="text" name="tags" size="20" id="tags" class="textinput" value="" />
                                <p id="add-tags-explanation" class="explanation"><?php echo __('Separate tags with %s', settings('tag_delimiter')); ?></p>
                                <input type="submit" name="add-tags-button" class="green button" id="add-tags-button" value="<?php echo __('Add Tags'); ?>" />
                            </div>
                            <div id="all-tags">
                            <?php if ($tags): ?>
                                <h4><?php echo __('All Tags'); ?></h4>
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
            
                    </div>

<?php foot();?>