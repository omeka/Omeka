<?php 
$pageTitle = __('Add Item Type');
head(array('title'=>$pageTitle,'bodyclass'=>'item-types')); ?>

<div id="primary">
    <form method="post" action="">
        <?php include 'form.php';?>
        <div>
            <input type="submit" name="submit" class="big green button" id="submit" value="<?php echo __('Add Item Type'); ?>" />
        </div>
    </form>
</div>
<?php foot(); ?>
