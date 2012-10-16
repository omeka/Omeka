<?php 
$pageTitle = __('Add a Collection');
echo head(array('title'=>$pageTitle, 'bodyclass'=>'collections')); ?>
        
            <form method="post">
                <?php include 'form.php';?>
                <div id="save" class="three columns omega">
                    <div class="panel">
                        <input type="submit" class="big green button" name="submit" value="<?php echo __('Save Collection'); ?>" />
                        
                        <?php fire_plugin_hook("admin_append_to_collections_panel_buttons", array('collection'=> get_current_record('collection'))); ?>
                                                                    
                        <div id="public-featured">
                            <?php echo $this->formLabel('public', __('Public')); ?>
                        <?php 
                            echo $this->formCheckbox('public', $collection->public, array(), array('1', '0'));
                        ?>

                            <?php echo $this->formLabel('featured', __('Featured')); ?>
                        <?php 
                            echo $this->formCheckbox('featured', $collection->featured, array(), array('1', '0')); 
                        ?>
                        </div>
                        <?php fire_plugin_hook("admin_append_to_collections_panel_fields", array('collection'=> get_current_record('collection'))); ?>
                        
                    </div>
                </div>
            </form>
<?php echo foot(); ?>
