<nav id="content-nav" class="two columns">
    <?php
        $mainNav = array(
            array(
                'label' => __('Dashboard'),
                'uri' => url('')
            ),
            array(
                'label' => __('Items'),
                'controller' => 'items',
                'route' => 'default',
            ),
            array(
                'label' => __('Collections'),
                'controller' => 'collections',
                'route' => 'default',
            ),
            array(
                'label' => __('Item Types'),
                'controller' => 'item-types',
                'route' => 'default',
            ),
            array(
                'label' => __('Tags'),
                'controller' => 'tags',
                'route' => 'default'
            )
        );
        $nav = nav($mainNav, 'admin_navigation_main');
        echo $nav;
    ?>
</nav>

<nav>
    <ul id="mobile-content-nav" class="quick-filter-wrapper"  name="mobile-nav">
        <li><a href="#" tabindex="0"><?php echo $title; ?></a>
        <?php echo $nav->setUlClass('dropdown'); ?>
        </li>
    </ul>    
</nav>
