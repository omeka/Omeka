<div id="section-nav">

<?php 
    $pluginNav = array(
        array(
        'label' => 'All',
        'title' => 'All Plugins',
        'module' => 'default',
        'controller' => 'plugins',
        'action' => 'browse'
        ),
        array(
        'label' => 'Active',
        'title' => 'Active Plugins',
        'module' => 'default',
        'controller' => 'plugins',
        'action' => 'active'
        ),
        array(
        'label' => 'Inactive',
        'title' => 'Inactive Plugins',
        'module' => 'default',
        'controller' => 'plugins',
        'action' => 'inactive'
        ),
        array(
        'label' => 'Uninstalled',
        'title' => 'Uninstalled Plugins',
        'module' => 'default',
        'controller' => 'plugins',
        'action' => 'uninstalled'
        )
    );

    $this->navigation()->menu()->setUlClass('navigation tabs');
    echo $this->navigation()->menu(new Omeka_Navigation($pluginNav)); 
?>

</div>