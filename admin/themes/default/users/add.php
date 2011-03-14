<?php
$pageTitle = __('Add New User');
head(array('title'=>$pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'users primary'));?>
<h1><?php echo $pageTitle; ?></h1>

<?php common('settings-nav'); ?>
<div id="primary">
<form method="post">
<?php include('form.php'); ?>
<input type="submit" name="submit" value="<?php echo __('Add this User'); ?>" />
</form>
</div>
<?php foot();?>