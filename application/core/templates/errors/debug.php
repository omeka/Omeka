<?php head(); ?>

<style type="text/css" media="screen">
    #wrap {
        width: 80%;
    }
</style>
    
<h1>Error Debugging - Use for Development Purposes Only</h1>

<h3><?php echo get_class($e) . ": ". nls2p($e->getMessage()); ?></h3>


<h4>Backtrace: <?php Zend_Debug::dump($e->getTraceAsString()); ?></h4>

    
<?php foot(); ?>