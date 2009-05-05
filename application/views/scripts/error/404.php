<?php head(array('title'=>'404!')); ?>
    <h1>Omeka 404</h1>
    
    &quot;<?php echo html_escape($badUri); ?>&quot; is not a valid URL.
    
    <?php if($msg = $e->getMessage()): ?>
        <p class="error">Error: <?php echo html_escape($msg); ?></p>
    <?php endif; ?>
<?php foot(); ?>