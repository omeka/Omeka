<?php 
$pageTitle = __('Add an Item');
echo head(array('title' => $pageTitle,'bodyclass' => 'items'));
include 'form-tabs.php';
echo flash();
?>

<form method="post" enctype="multipart/form-data" id="item-form" action="">
    <?php include 'form.php'; ?>
    <section class="three columns omega">
        <div id="save" class="panel">
            <input type="submit" name="submit" class="submit big green button" id="add_item" value="<?php echo __('Add Item'); ?>" />        
            
            <?php fire_plugin_hook("admin_items_panel_buttons", array('view'=>$this, 'record'=>$item)); ?>
            
            <div id="public-featured">
                <?php if ( is_allowed('Items', 'makePublic') ): ?>
                    <div class="public">
                        <label for="public"><?php echo __('Public'); ?>:</label> 
                        <?php echo $this->formCheckbox('public', $item->public, array(), array('1', '0')); ?>
                    </div>
                <?php endif; ?>
                <?php if ( is_allowed('Items', 'makeFeatured') ): ?>
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
            <?php fire_plugin_hook("admin_items_panel_fields", array('view'=>$this, 'record'=>$item)); ?>
        </div>
    </section>
</form>

<?php echo foot();?>
