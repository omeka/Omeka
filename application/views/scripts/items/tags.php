<?php
$pageTitle = __('Browse Items');
echo head(['title' => $pageTitle, 'bodyclass' => 'items tags']);
?>

<h1><?php echo $pageTitle; ?></h1>

<nav class="navigation items-nav secondary-nav">
    <?php echo public_nav_items(); ?>
</nav>

<?php echo tag_cloud($tags, 'items/browse'); ?>

<?php echo foot(); ?>
