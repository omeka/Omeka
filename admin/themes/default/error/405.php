<?php
echo head(array('bodyclass'=>'error405', 'title' => __('405: Method Not Allowed')));
echo flash();
?>

<p>
<?php 
$adminEmail = '<a href="mailto:'.option('administrator_email') .'">'.__('site administrator') .'</a>';
echo __('You must use a different method to access this URL. If you reached this page by following a link or clicking a button in Omeka, please contact the %s.', $adminEmail);
?>
</p>

<?php echo foot(); ?>
