<?php
    $itemTitle = strip_formatting(item(array('Dublin Core', 'Title')));
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
                        <?php include 'tag-form.php'; ?>
                    </div>

<?php foot();?>
