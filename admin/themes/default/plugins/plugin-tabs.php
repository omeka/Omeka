<div id="section-nav">

<?php 
    $pluginNav = array(
        array(
        'label' => __('All'),
        'module' => 'default',
        'controller' => 'plugins',
        'action' => 'index'
        ),
        array(
        'label' => __('Active'),
        'module' => 'default',
        'controller' => 'plugins',
        'action' => 'active'
        ),
        array(
        'label' => __('Inactive'),
        'module' => 'default',
        'controller' => 'plugins',
        'action' => 'inactive'
        ),
        array(
        'label' => __('Uninstalled'),
        'module' => 'default',
        'controller' => 'plugins',
        'action' => 'uninstalled'
        )
    );

    echo nav($pluginNav);
?>

</div>
