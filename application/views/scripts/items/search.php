<?php
$pageTitle = __('Search Items');
echo head(array('title' => $pageTitle,
           'bodyclass' => 'items advanced-search',
           'bodyid' => 'items'));
?>

<h1><?php echo $pageTitle; ?></h1>

<nav class="items-nav navigation" id="secondary-nav">
    <?php echo public_nav_items(); ?>
</nav>

<?php echo $this->partial('items/search-form.php',
    array('formAttributes' =>
        array('id'=>'advanced-search-form'))); ?>

<?php echo foot(); ?>
