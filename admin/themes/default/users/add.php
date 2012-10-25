<?php
$pageTitle = __('Add New User');
echo head(array('title'=>$pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'users primary'));?>

<?php echo flash(); ?>
<section class="seven columns alpha">
<?php echo $this->form; ?>
</section>

<?php echo foot();?>
