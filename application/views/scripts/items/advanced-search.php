<?php
$pageTitle = __('Advanced Search');
echo head(array('title' => $pageTitle,
           'bodyclass' => 'advanced-search',
           'bodyid' => 'advanced-search-page'));
?>
<div id="primary">
<h1><?php echo $pageTitle; ?></h1>

    <nav class="items-nav navigation" id="secondary-nav">
        <?php echo public_nav_items(); ?>
    </nav>

<?php echo $this->partial('items/advanced-search-form.php',
    array('formAttributes' =>
        array('id'=>'advanced-search-form'))); ?>

</div> <!-- Close 'primary' div. -->
<?php echo foot();
