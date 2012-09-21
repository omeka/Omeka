<?php
$pageTitle = __('Search Settings');
echo head(array('title' => $pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'settings primary'));
?>
<div class="seven columns alpha">
<?php echo common('settings-nav'); ?>
<?php echo flash(); ?>
<form method="post">
    <input type="submit" name="index_search_text" value="Index Search Text"/>
</form>
</div>
<?php echo foot(); ?>
