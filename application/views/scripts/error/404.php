<?php head(array('title'=>'404 Page Not Found')); ?>
<div id="primary">
    <h1>404 Page Not Found</h1>
    <p>&#8220;<?php echo html_escape($badUri); ?>&#8221; is not a valid URL.</p>
</div>
<?php foot(); ?>
