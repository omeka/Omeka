<nav id="content-nav" class="two columns" role="navigation" aria-label="<?php echo __('Main Menu'); ?>">
	<button type="button" id="content-nav-toggle" class="mobile-menu" data-target=".navigation" aria-expanded="false" title="<?php echo __('Main Menu'); ?>"><span class="sr-only"><?php echo __('Main Menu'); ?> <?php echo __('Current Page'); ?> </span><?php echo $title; ?></button>
    <?php
        $mainNav = array(
            array(
                'label' => __('Dashboard'),
                'uri' => url('')
            ),
            array(
                'label' => __('Items'),
                'uri' => url('items')
            ),
            array(
                'label' => __('Collections'),
                'uri' => url('collections')
            ),
            array(
                'label' => __('Item Types'),
                'uri' => url('item-types')
            ),
            array(
                'label' => __('Tags'),
                'uri' => url('tags')
            )
        );
        $nav = nav($mainNav, 'admin_navigation_main');
        echo $nav;
    ?>
</nav>
