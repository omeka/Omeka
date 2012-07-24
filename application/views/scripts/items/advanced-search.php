<?php
$pageTitle = __('Advanced Search');
head(array('title' => $pageTitle,
           'bodyclass' => 'advanced-search',
           'bodyid' => 'advanced-search-page'));
?>
<div id="primary">
<h1><?php echo $pageTitle; ?></h1>

<?php echo $this->partial('items/advanced-search-form.php',
    array('formAttributes' =>
        array('id'=>'advanced-search-form'))); ?>

</div> <!-- Close 'primary' div. -->
<?php foot();
