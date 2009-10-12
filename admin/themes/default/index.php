<?php head(array('bodyclass'=>'index primary-secondary', 'title'=>'Dashboard')); ?>
<h1>Dashboard</h1>
    <div id="primary">
        
        <?php // Retrieve the latest version of Omeka by pinging the Omeka server. ?>
        <?php if (has_permission('Upgrade', 'index')):
              $latestVersion = get_latest_omeka_version();
                  if ($latestVersion and version_compare(OMEKA_VERSION, $latestVersion, '<')): ?>
                    <div class="success">
                        There is a new version of Omeka available for download
                        (<?php echo $latestVersion; ?>).
                        <a href="http://omeka.org/download/">Upgrade</a>
                    </div>
        <?php       endif; 
              endif; ?>
            
            <div id="getting-started">
                <h2>Getting Started with Omeka</h2>
                <dl>
                    <dt class="items"><?php echo link_to('items', null, 'Items'); ?></dt>
                    <dd class="items">
                        <ul>
                            <li><a class="add" href="<?php echo html_escape(uri('items/add')); ?>">Add a new item to your archive</a></li>
                            <li><a class="browse" href="<?php echo html_escape(uri('items/browse')); ?>">Browse your items</a></li>
                            
                        </ul>
                        <p>Manage items in your archive: add, edit, and delete items.</p>
                    </dd>
                
                <?php if(has_permission('Collections','browse')): ?>
                    <dt class="collections"><?php echo link_to('collections', null, 'Collections'); ?></dt>
                    <dd class="collections">
                        <ul>
                            <li><a class="add-collection" href="<?php echo html_escape(uri('collections/add')); ?>">Add a collection to group items</a></li>
                            <li><a class="browse" href="<?php echo html_escape(uri('collections/browse')); ?>">Browse your collections</a></li>
                            
                        </ul>
                        <p>Manage collections in your archive: add, edit, and delete collections.</p>
                    </dd>
                
                <?php endif; ?>
                
                <?php if(has_permission('Users','browse')): ?>
                    <dt class="users"><a href="<?php echo html_escape(uri('users/browse')); ?>">Users</a></dt>
                    <dd class="users">
                        <ul>
                            <li><a class="browse-users" href="<?php echo html_escape(uri('users/browse')); ?>">Browse Users</a></li>
                            <li><a class="add-user" href="<?php echo html_escape(uri('users/add')); ?>">Add a User</a></li>
                        </ul>
                        <p>Add and manage users of various levels: from researcher to super.</p>
                    </dd>
                <?php endif; ?>
                
                <?php if(has_permission('super')): ?>
                    <dt class="site-settings"><a href="<?php echo html_escape(uri('settings')); ?>">Settings</a></dt>
                    <dd class="site-settings">
                        <ul>
                            <li><a class="editsettings" href="<?php echo html_escape(uri('settings')); ?>">Edit General Settings</a></li>
                            <li><a class="managethemes" href="<?php echo html_escape(uri('themes')); ?>">Manage Themes</a></li>
                            <li><a class="manageplugins" href="<?php echo html_escape(uri('plugins')); ?>">Manage Plugins</a></li>
                        </ul>
                        <p>Manage your general settings for the site, including title, description, and themes.</p>
                    </dd>
            <?php endif; ?>
            <?php fire_plugin_hook('admin_append_to_dashboard_primary'); ?>
            </dl>
            </div>
        </div>
        
        <div id="secondary">
            <div id="site-meta" class="info-panel">
                <h2>Site Overview</h2>
                <p><em><?php echo settings('site_title'); ?></em> 
                contains <?php echo total_items(); ?> items, in <?php echo total_collections(); ?> 
                collections, tagged with <?php echo total_tags(); ?> keywords. 
                There are <?php echo total_users(); ?> users. This is
                Omeka version <em><?php echo OMEKA_VERSION; ?></em>.</p>
            </div>
            <div id="recent-items" class="info-panel">
                <h2>Recent Items</h2>
                <?php set_items_for_loop(recent_items('5')); ?>
                <?php if(!has_items_for_loop()):?>
                    <p>There are no items to display.</p>   
                <?php else: ?>
                <ul>
                    <?php $key = 0; ?>
                    <?php while(loop_items()): ?>
                        <li class="<?php echo is_odd($key++) ? 'even' : 'odd'; ?>">
                            <?php echo link_to_item();?>
                        </li>   
                    <?php endwhile; ?>
                </ul>
                
                <p id="view-all-items"><a href="<?php echo html_escape(uri('items/browse')); ?>">View All Items</a></p>
                <?php endif; ?>
            </div>
            
            <div id="tag-cloud" class="info-panel">
                <h2>Recent Tags</h2>
                <?php echo tag_cloud(recent_tags(), uri('items/browse/')); ?>
            </div>
            
            <?php fire_plugin_hook('admin_append_to_dashboard_secondary'); ?>
        </div>
<?php foot(); ?>