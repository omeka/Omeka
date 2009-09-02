<?php head(array('title'=>'Add Item','content_class' => 'vertical-nav', 'bodyclass'=>'items primary'));?>

<script type="text/javascript">

document.observe('dom:loaded',function(){

     new Control.Tabs('section-nav');  
}

);
document.write('<style>#api, #resources { display:none; }</style>');
</script>
<h1>Add an Item</h1>
<?php include 'form-tabs.php'; ?>
<div id="primary">

        <form method="post" enctype="multipart/form-data" id="item-form">
            <?php include('form.php'); ?>
            <input type="submit" name="submit" class="submit submit-medium" id="add_item" value="Add Item" />
        </form>
</div>

<?php foot();?>
