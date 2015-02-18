<nav id="content-nav" class="two columns" role="navigation" aria-label="<?php echo __('Main Menu'); ?>">
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

<nav id="mobile-content-nav">
    <ul class="quick-filter-wrapper">
        <li><a href="#" tabindex="0"><?php echo $title; ?></a>
        <?php echo $nav->setUlClass('dropdown'); ?>
        </li>
    </ul>    
</nav>
