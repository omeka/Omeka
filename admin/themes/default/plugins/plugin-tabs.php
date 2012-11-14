<div id="section-nav">

<?php 
    $pluginNav = array(
        array(
        'label' => __('All'),
        'uri' => url('plugins/browse')
        ),
        array(
        'label' => __('Active'),
        'uri' => url('plugins/active')
        ),
        array(
        'label' => __('Inactive'),
        'uri' => url('plugins/inactive')
        ),
        array(
        'label' => __('Uninstalled'),
        'uri' => url('plugins/uninstalled')
        )
    );

    echo nav($pluginNav);
?>

</div>
