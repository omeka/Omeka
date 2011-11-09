<?php 
$pageTitle = __('Add Item Type');
head(array('title'=>$pageTitle,'bodyclass'=>'item-types')); ?>
<h1><?php echo $pageTitle; ?></h1>

<div id="primary">
    <form method="post" action="">
        <?php include 'form.php';?>
        <div>
            <input type="submit" name="submit" class="submit" id="submit" value="<?php echo __('Add Item Type'); ?>" />
        </div>
    </form>
</div>
<?php foot(); ?>
