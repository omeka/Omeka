<?php
$pageTitle = __('Advanced Search');
echo head(array('title' => $pageTitle,
           'bodyclass' => 'advanced-search',
           'bodyid' => 'advanced-search-page'));
?>
<div id="primary">

<?php echo $this->partial('items/advanced-search-form.php',
    array('formAttributes' =>
        array('id'=>'advanced-search-form'))); ?>
</div>
<?php echo foot(); ?>
