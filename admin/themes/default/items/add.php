<?php 
$pageTitle = __('Add an Item');
head(array('title'=>$pageTitle,'content_class' => 'vertical-nav', 'bodyclass'=>'items primary'));?>

<h1><?php echo $pageTitle; ?></h1>
<?php include('form-tabs.php'); ?>
<div id="primary">

        <form method="post" enctype="multipart/form-data" id="item-form" action="">
            <?php include('form.php'); ?>
            <div>
                <input type="submit" name="submit" class="submit" id="add_item" value="<?php echo __('Add Item'); ?>" />
            </div>
        </form>
</div>

<?php foot();?>
