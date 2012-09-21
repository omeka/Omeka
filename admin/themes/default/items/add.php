<?php 
$pageTitle = __('Add an Item');
echo head(array('title'=>$pageTitle,'content_class' => 'vertical-nav', 'bodyclass'=>'items primary'));?>

<?php include('form-tabs.php'); ?>
        
            <form method="post" enctype="multipart/form-data" id="item-form" action="">
                <?php include 'form.php'; ?>
                
                <div id="save" class="three columns omega">
                
                    <div class="panel">
                    
                    <input type="submit" name="submit" class="submit big green button" id="add_item" value="<?php echo __('Add Item'); ?>" />        
                    
                    <div id="public-featured">
                        <?php if ( has_permission('Items', 'makePublic') ): ?>
                            <div class="public">
                                <label for="public"><?php echo __('Public'); ?>:</label> 
                                <?php echo $this->formCheckbox('public', $item->public, array(), array('1', '0')); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ( has_permission('Items', 'makeFeatured') ): ?>
                            <div class="featured">
                                <label for="featured"><?php echo __('Featured'); ?>:</label> 
                                <?php echo $this->formCheckbox('featured', $item->featured, array(), array('1', '0')); ?>
                            </div>
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
                    </div>
                                    
        </form>

<?php echo foot();?>
