<nav id="content-nav" class="two columns">

    <ul>
        <?php
            $contentNav = array(
                __('Dashboard') => uri(''),
                __('Items') => uri('items'),
                __('Collections') => uri('collections'),
                __('Item Types') => uri('item-types'),
                __('Tags') => uri('tags'),
                __('Navigation') => uri('navigation'),
                
                );
            echo nav(apply_filters('admin_navigation_main', $contentNav));
        ?>
    </ul>

</nav>

<nav>
    <ul id="mobile-content-nav" class="quick-filter-wrapper"  name="mobile-nav">
        <li><a href="#" tabindex="0"><?php echo $title; ?></a>
        <ul class="dropdown">
        <?php
            $contentNav = array(
                __('Dashboard') => uri(''),
                __('Items') => uri('items'),
                __('Collections') => uri('collections'),
                __('Item Types') => uri('item-types'),
                __('Tags') => uri('tags'),
                __('Navigation') => uri('navigation')
                );
            echo nav(apply_filters('admin_navigation_main', $contentNav));
        ?>            
        </ul>
        </li>
    </ul>    
</nav>