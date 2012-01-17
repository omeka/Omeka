<?php 
$pageTitle = __('Add a Collection');
head(array('title'=>$pageTitle, 'bodyclass'=>'collections')); ?>
<h1><?php echo $pageTitle; ?></h1>

<div id="primary">
<form method="post">
<?php include 'form.php';?>
<input type="submit" class="submit" name="submit" value="<?php echo __('Save Collection'); ?>" />
</form>
</div>
<?php foot(); ?>
