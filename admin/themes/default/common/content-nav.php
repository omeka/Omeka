    <nav class="content-nav two columns">
    
        <ul>
            <?php
                $contentNav = array(
                    __('Dashboard') => uri(''),
                    __('Items') => uri('items'),
                    __('Collections') => uri('collections'),
                    __('Item Types') => uri('item-types'),
                    __('Tags') => uri('tags')
                    );
                echo nav(apply_filters('admin_navigation_main', $contentNav));
            ?>
        </ul>
    
    </nav>

    <nav>
        <select id="mobile-content-nav"  name="mobile-nav">
            <option value="mobile">Dashboard</option>
            <option value="mobile">Items</option>
            <option value="mobile">- Add Items</option>
            <option value="mobile">Collections</option>
            <option value="mobile">- Add Collections</option>
            <option value="mobile">Item Types</option>
            <option value="mobile">Tags</option>
        </select>    
    </nav>