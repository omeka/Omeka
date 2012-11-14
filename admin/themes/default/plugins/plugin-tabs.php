<div id="section-nav">

<?php 
    $pluginNav = array(
        array(
        'label' => __('All'),
        'module' => 'default',
        'controller' => 'plugins',
        'action' => 'index',
        'theme' => 'admin'
        ),
        array(
        'label' => __('Active'),
        'module' => 'default',
        'controller' => 'plugins',
        'action' => 'active',
        'theme' => 'admin'
        ),
        array(
        'label' => __('Inactive'),
        'module' => 'default',
        'controller' => 'plugins',
        'action' => 'inactive',
        'theme' => 'admin'
        ),
        array(
        'label' => __('Uninstalled'),
        'module' => 'default',
        'controller' => 'plugins',
        'action' => 'uninstalled',
        'theme' => 'admin'
        )
    );

    echo nav($pluginNav);
?>

</div>
