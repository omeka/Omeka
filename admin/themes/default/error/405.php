<?php
$pageTitle = __('405: Method Not Allowed');
head(array('bodyclass'=>'error405 primary', 'title'=>$pageTitle)); ?>
<h1><?php echo $pageTitle; ?></h1>

<div id="primary" class="method-not-allowed">
    <?php echo flash(); ?>
    
<p><?php 
$adminEmail = '<a href="mailto:'.settings('administrator_email') .'">'.__('site administrator') .'</a>';
echo __('You must use a different method to access this URL. If you reached this page by following a link or clicking a button in Omeka, please contact the %s.', $adminEmail); ?></p>
</div>

<?php foot();
