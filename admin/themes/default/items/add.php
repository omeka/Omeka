<?php head(array('title'=>'Add Item','content_class' => 'vertical-nav', 'bodyclass'=>'items primary'));?>

<h1>Add an Item</h1>
<?php include('form-tabs.php'); ?>
<div id="primary">

        <form method="post" enctype="multipart/form-data" id="item-form" action="">
            <?php include('form.php'); ?>
            <div>
                <input type="submit" name="submit" class="submit" id="add_item" value="Add Item" />
            </div>
        </form>
</div>

<?php foot();?>
