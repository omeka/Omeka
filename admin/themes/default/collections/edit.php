<?php
    $collectionTitle = strip_formatting(metadata('collection', array('Dublin Core', 'Title')));
    if ($collectionTitle != '' && $collectionTitle != __('[Untitled]')) {
        $collectionTitle = ': &quot;' . $collectionTitle . '&quot; ';
    } else {
        $collectionTitle = '';
    }
    $collectionTitle = __('Edit Collection #%s', metadata('collection', 'id')) . $collectionTitle;
?>

<?php echo head(array('title'=> $collectionTitle, 'bodyclass'=>'collections')); ?>

        <?php include('form-tabs.php'); ?>

        <form method="post">
            
            <?php include 'form.php';?>

            <div id="save" class="three columns omega">

                <div class="panel">
                    <input type="submit" name="submit" class="big green button" id="save-changes" value="<?php echo __('Save Changes'); ?>" />
                    <a href="<?php echo html_escape(public_url('collections/show/'.metadata('collection', 'id'))); ?>" class="big blue button" target="_blank"><?php echo __('View Public Page'); ?></a>
                    <?php echo link_to_collection(__('Delete'), array('class' => 'big red button'), 'delete-confirm'); ?>
                    
                    <?php fire_plugin_hook("admin_append_to_collections_panel_buttons", array('collection'=> get_current_record('collection'))); ?>
                                        
                    <div id="public-featured">
                        <div class="public">
                            <?php echo $this->formLabel('public', __('Public')); ?>
                            <?php echo $this->formCheckbox('public', $collection->public, array(), array('1', '0')); ?>
                        </div>

                        <div class="featured">
                            <?php echo $this->formLabel('featured', __('Featured')); ?>
                            <?php echo $this->formCheckbox('featured', $collection->featured, array(), array('1', '0')); ?>
                        </div>
                    </div>
                    <?php fire_plugin_hook("admin_append_to_collections_panel_fields", array('collection'=> get_current_record('collection'))); ?>
                </div>

            </div>

        </form>

<?php echo foot(); ?>
