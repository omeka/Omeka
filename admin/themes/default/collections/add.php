<?php 
$pageTitle = __('Add a Collection');
head(array('title'=>$pageTitle, 'bodyclass'=>'collections')); ?>
        
            <form method="post">
                <?php include 'form.php';?>
                <div id="save" class="three columns omega">
                    <div class="panel">
                        <input type="submit" class="big green button" name="submit" value="<?php echo __('Save Collection'); ?>" />
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
