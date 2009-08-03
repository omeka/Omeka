<?php 
if (!$isPartial): // If we are using the partial view of this search form.
head(array('title'=>'Advanced Search', 'bodyclass' => 'advanced-search', 'bodyid' => 'advanced-search-page')); ?>
<script type="text/javascript" charset="utf-8">
    Event.observe(window, 'load', Omeka.Search.activateSearchButtons);
</script>
<h1>Advanced Search</h1>

<div id="primary">
   
<?php endif; ?>

<?php

$navs = array('Items' => uri('search/?form=Item'), 'Collections' => uri('search/?form=Collection'));

$navs = apply_filters('public_search_navigation', $navs);

echo '<ul class="navigation">'.nav($navs).'</ul>';
?>

<?php
if(isset($_GET['form'])) {
    $formName = $_GET['form'];
}else {
    $formName = 'Item';
}
?>

<?php 
switch($formName) { 
    case 'Item':
        include 'items-search-form.php';
    break;
    
    case 'Collection':
        include 'collections-search-form.php';
    break;
    
    default:
        fire_plugin_hook('public_search_form', $formName, $formAttributes);
    break;
}
?>

<?php if (!$isPartial): ?>
    </div> <!-- Close 'primary' div. -->
    <?php foot(); ?>
<?php endif; ?>