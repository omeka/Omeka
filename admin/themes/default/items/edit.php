<?php
    $itemTitle = strip_formatting(item('Dublin Core', 'Title'));
    if ($itemTitle != '' && $itemTitle != '[Untitled]') {
        $itemTitle = ': &quot;' . $itemTitle . '&quot; ';
    } else {
        $itemTitle = '';
    }
    $itemTitle = 'Edit Item #' . item('id') . $itemTitle;
?>
<?php head(array('title'=> $itemTitle, 'bodyclass'=>'items primary','content_class' => 'vertical-nav'));?>
<h1><?php echo $itemTitle; ?></h1>

<?php include 'form-tabs.php'; // Definitions for all the tabs for the form. ?>

<div id="primary">

    <form method="post" enctype="multipart/form-data" id="item-form" action="">
        <?php include 'form.php'; ?>
        <div>
            <?php echo submit(array('name'=>'submit', 'id'=>'save-changes', 'class'=>'submit submit-medium'), 'Save Changes'); ?>
        </div>
        <p id="delete_item_link">
            <?php echo link_to_item('Delete This Item', 
                array('class'=>'delete delete-item'), 'delete'); ?>
        </p>
    </form>



</div>

<?php foot();?>
