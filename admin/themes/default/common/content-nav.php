<nav id="content-nav" class="two columns">

    <ul>
        <?php
            $contentNav = array(
                __('Dashboard') => url(''),
                __('Items') => url('items'),
                __('Collections') => url('collections'),
                __('Item Types') => url('item-types'),
                __('Tags') => url('tags'),
                
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
                __('Dashboard') => url(''),
                __('Items') => url('items'),
                __('Collections') => url('collections'),
                __('Item Types') => url('item-types'),
                __('Tags') => url('tags'),
                );
            echo nav(apply_filters('admin_navigation_main', $contentNav));
        ?>            
        </ul>
        </li>
    </ul>    
</nav>