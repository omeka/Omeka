<nav id="content-nav" class="two columns" role="navigation" aria-label="<?php echo __('Main Menu'); ?>">
	<button type="button" id="content-nav-toggle" class="mobile-menu" data-target=".navigation" aria-expanded="false" title="<?php echo __('Main Menu'); ?>"><span class="sr-only"><?php echo __('Main Menu'); ?> <?php echo __('Current Page'); ?> </span><?php echo $title; ?></button>
    <?php
        $mainNav = [
            [
                'label' => __('Dashboard'),
                'uri' => url('')
            ],
            [
                'label' => __('Items'),
                'uri' => url('items')
            ],
            [
                'label' => __('Collections'),
                'uri' => url('collections')
            ],
            [
                'label' => __('Item Types'),
                'uri' => url('item-types')
            ],
            [
                'label' => __('Tags'),
                'uri' => url('tags')
            ]
        ];
        $nav = nav($mainNav, 'admin_navigation_main');
        echo $nav;
    ?>
</nav>
