<?php
$pageTitle = __('Add New User');
head(array('title'=>$pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'users primary'));?>
<h1><?php echo $pageTitle; ?></h1>

<?php common('settings-nav'); ?>
<div id="primary">
<?php echo $this->form; ?>
</div>
<?php foot();?>
