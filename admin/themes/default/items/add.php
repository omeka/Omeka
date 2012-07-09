<?php 
$pageTitle = __('Add an Item');
head(array('title'=>$pageTitle,'content_class' => 'vertical-nav', 'bodyclass'=>'items primary'));?>

<?php include('form-tabs.php'); ?>
        
            <form method="post" enctype="multipart/form-data" id="item-form" action="">
                <?php include 'form.php'; ?>
                
                <div id="save" class="three columns omega">
                
                    <div class="panel">
                    
                    <input type="submit" name="submit" class="submit big green button" id="add_item" value="<?php echo __('Add Item'); ?>" />        
                    
                    <div id="public-featured">
                        <?php if ( has_permission('Items', 'makePublic') ): ?>
                                <label for="public"><?php echo __('Public'); ?>:</label> 
                                <?php echo $this->formCheckbox(array('name'=>'public', 'id'=>'public'), $item->public); ?>
                        <?php endif; ?>
                        <?php if ( has_permission('Items', 'makeFeatured') ): ?>
                                <label for="featured"><?php echo __('Featured'); ?>:</label> 
                                <?php echo $this->formCheckbox(array('name'=>'featured', 'id'=>'featured'), $item->featured); ?>
                        <?php endif; ?>                        
                        
                    </div>
                    
                        <div id="collection-form" class="field">
                            <?php echo $this->formLabel('collection-id', __('Collection'));?>
                            <div class="inputs">
                                <?php echo $this->formSelect(
                                    'collection_id',
                                    $item->collection_id,
                                    array('id' => 'collection-id'),
                                    get_table_options('Collection')
                                );?>
                            </div>                        
                        </div>
                        
                        <div id="tag-form" class="field">
                            <input type="hidden" name="tags-to-add" size="30" id="tags-to-add" value="" />
                            <input type="hidden" name="tags-to-delete" size="30" id="tags-to-delete" value="" />
                            <div id="add-tags">
                                <label><?php echo __('Add Tags'); ?></label>           
                                <input type="text" name="tags" size="20" id="tags" class="textinput" value="" />
                                <p id="add-tags-explanation" class="explanation"><?php echo __('Separate tags with %s', settings('tag_delimiter')); ?></p>
                                <input type="submit" name="add-tags-button" id="add-tags-button" class="green button" value="<?php echo __('Add Tags'); ?>" />
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
            
                    </div>
                                    
        </form>

<?php foot();?>
