<?php head(array('title' => 'Debug', 'bodyclass' => 'debug', 'bodyid' => 'debug')); ?>

<style type="text/css" media="screen">
    #wrap {
        width: 80%;
    }
</style>
    
<h1>Omeka Has Encountered an Error</h1>

<h3><?php echo get_class($e) . ": ". nls2p($e->getMessage()); ?></h3>

<h4>Backtrace:</h4>
<pre><?php echo $e->getTraceAsString(); ?></pre>

    
<?php foot(); ?>