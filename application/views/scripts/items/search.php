<?php
$pageTitle = __('Search Items');
echo head(['title' => $pageTitle,
           'bodyclass' => 'items advanced-search']);
?>

<h1><?php echo $pageTitle; ?></h1>

<nav class="items-nav navigation secondary-nav" aria-label="<?php echo __('Items'); ?>">
    <?php echo public_nav_items(); ?>
</nav>

<?php echo $this->partial('items/search-form.php', [
    'formAttributes' => [
        'id' => 'advanced-search-form', 
        'role' => 'search',
        'aria-label' => __('Items')
    ]
]); ?>

<?php echo foot(); ?>
