<?php
echo head(array('bodyclass' => 'error403', 'title' => __('403: Forbidden')));
echo flash();
?>

<p><?php echo __('Sorry, you do not have permission to access this page.'); ?></p>

<p>
<?php 
$siteAdministratorEmail = '<a href="mailto:'. option('administrator_email') . '">' . __('site administrator') . '</a>';
echo __('If you think you should have permission, try contacting the %s.', $siteAdministratorEmail);
?>
</p>

<?php echo foot(); ?>
