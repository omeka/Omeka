<?php head(array('bodyclass'=>'error405 primary', 'title'=>'Method Not Allowed')); ?>
<h1>405: Method Not Allowed</h1>

<div id="primary" class="method-not-allowed">
    <?php echo flash(); ?>
    
<p>You must use a different method to access this URL. If you reached this page by following a link or clicking a button in Omeka, please contact the <a href="mailto:<?php echo settings('administrator_email'); ?>">site administrator</a>.</p>
</div>

<?php foot();
