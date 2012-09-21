<?php
$pageTitle = __('Add New User');
echo head(array('title'=>$pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'users primary'));?>

<div class="seven columns alpha">
<?php echo $this->form; ?>
</div>

<?php echo foot();?>
