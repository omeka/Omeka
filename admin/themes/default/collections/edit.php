<?php
    $collectionTitle = strip_formatting(metadata('collection', 'Name'));
    if ($collectionTitle != '') {
        $collectionTitle = ': &quot;' . $collectionTitle . '&quot; ';
    } else {
        $collectionTitle = '';
    }
    $collectionTitle = __('Edit Collection #%s', metadata('collection', 'id')) . $collectionTitle;
?>

<?php head(array('title'=> $collectionTitle, 'bodyclass'=>'collections')); ?>
        
        <form method="post">
            

            <?php include 'form.php';?>
                
            <div id="save" class="three columns omega">
            
                <div class="panel">
                    <input type="submit" name="submit" class="big green button" id="save-changes" value="<?php echo __('Save Changes'); ?>" />
                    <a href="<?php echo html_escape(public_uri('collections/show/'.metadata('collection', 'id'))); ?>" class="big blue button" target="_blank"><?php echo __('View Public Page'); ?></a>
                    <?php echo link_to_collection(__('Delete Collection'), array('class' => 'big red button'), 'delete-confirm'); ?>
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

                </div>            
                
            </div>            
            
        </form>

<?php foot(); ?>
