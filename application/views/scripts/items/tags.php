<?php
$pageTitle = __('Browse Items');
head(array('title'=>$pageTitle,'bodyid'=>'items','bodyclass'=>'tags'));
?>

<div id="primary">

    <h1><?php echo $pageTitle; ?></h1>

    <ul class="navigation item-tags" id="secondary-nav">
        <?php echo public_nav_items(); ?>
    </ul>

    <?php echo tag_cloud($tags, url('items/browse')); ?>

</div><!-- end primary -->

<?php foot(); ?>
