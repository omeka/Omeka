<?php
$pageTitle = __('Dashboard');
head(array('bodyclass'=>'index primary-secondary', 'title'=>$pageTitle)); ?>
<h1><?php echo $pageTitle;; ?></h1>
    <div id="primary">
        
        <?php // Retrieve the latest version of Omeka by pinging the Omeka server. ?>
        <?php if (has_permission('Upgrade', 'index')):
              $latestVersion = get_latest_omeka_version();
                  if ($latestVersion and version_compare(OMEKA_VERSION, $latestVersion, '<')): ?>
                    <div class="success">
                        <?php echo __('A new version of Omeka is available for download.'); ?>
                        <a href="http://omeka.org/download/"><?php echo __('Upgrade to %s', $latestVersion); ?></a>
                    </div>
        <?php       endif; 
              endif; ?>
        <?php echo flash(); ?>    
            <div id="getting-started">
                <h2><?php echo __('Getting Started with Omeka'); ?></h2>
                <dl>
                    <dt class="items"><?php echo link_to('items', null, __('Items')); ?></dt>
                    <dd class="items">
                        <ul>
                            <li><a class="add" href="<?php echo html_escape(uri('items/add')); ?>"><?php echo __('Add a new item to your archive'); ?></a></li>
                            <li><a class="browse" href="<?php echo html_escape(uri('items/browse')); ?>"><?php echo __('Browse your items'); ?></a></li>
                            
                        </ul>
                        <p><?php echo __('Manage items in your archive: add, edit, and delete items.'); ?></p>
                    </dd>
                
                <?php if(has_permission('Collections','browse')): ?>
                    <dt class="collections"><?php echo link_to('collections', null, __('Collections')); ?></dt>
                    <dd class="collections">
                        <ul>
                            <li><a class="add-collection" href="<?php echo html_escape(uri('collections/add')); ?>"><?php echo __('Add a collection to group items'); ?></a></li>
                            <li><a class="browse" href="<?php echo html_escape(uri('collections/browse')); ?>"><?php echo __('Browse your collections'); ?></a></li>
                            
                        </ul>
                        <p><?php echo __('Manage collections in your archive: add, edit, and delete collections.'); ?></p>
                    </dd>
                
                <?php endif; ?>
                
                <?php if(has_permission('Users','browse')): ?>
                    <dt class="users"><a href="<?php echo html_escape(uri('users/browse')); ?>"><?php echo __('Users'); ?></a></dt>
                    <dd class="users">
                        <ul>
                            <li><a class="browse-users" href="<?php echo html_escape(uri('users/browse')); ?>"><?php echo __('Browse Users'); ?></a></li>
                            <?php if (has_permission('Users', 'add')): ?>
                            <li><a class="add-user" href="<?php echo html_escape(uri('users/add')); ?>"><?php echo __('Add a User'); ?></a></li>
                            <?php endif; ?>
                        </ul>
                        <p><?php echo __('Manage users of various levels: from researcher to super.'); ?></p>
                    </dd>
                <?php endif; ?>
                
                <?php if(has_permission('Settings', 'edit')): ?>
                    <dt class="site-settings"><a href="<?php echo html_escape(uri('settings')); ?>"><?php echo __('Settings'); ?></a></dt>
                    <dd class="site-settings">
                        <ul>
                            <li><a class="editsettings" href="<?php echo html_escape(uri('settings')); ?>"><?php echo __('Edit General Settings'); ?></a></li>
                            <li><a class="managethemes" href="<?php echo html_escape(uri('themes')); ?>"><?php echo __('Manage Themes'); ?></a></li>
                            <li><a class="manageplugins" href="<?php echo html_escape(uri('plugins')); ?>"><?php echo __('Manage Plugins'); ?></a></li>
                        </ul>
                        <p><?php echo __('Manage your general settings for the site, including title, description, and themes.'); ?></p>
                    </dd>
            <?php endif; ?>
            <?php fire_plugin_hook('admin_append_to_dashboard_primary'); ?>
            </dl>
            </div>
        </div>
        
        <div id="secondary">
            <div id="site-meta" class="info-panel">
                <h2><?php echo __('Site Overview'); ?></h2>
                <p>
                <?php 
                /**
                 * $siteTitle = settings('site_title');
                 * total_items()
                 * total_collections()
                 * total_tags()
                 * total_users()
                 */
                echo __('<em>%1$s</em> contains %2$s items, in %3$s collections, tagged with %4$s keywords. There are %5$s users.', settings('site_title'), total_items(), total_collections(), total_tags(), total_users()); ?></p>
            </div>
            <div id="recent-items" class="info-panel">
                <h2><?php echo __('Recent Items'); ?></h2>
                <?php set_items_for_loop(recent_items('5')); ?>
                <?php if(!has_items_for_loop()):?>
                    <p><?php echo __('There are no items to display.'); ?></p>   
                <?php else: ?>
                <ul>
                    <?php $key = 0; ?>
                    <?php while(loop_items()): ?>
                        <li class="<?php echo is_odd($key++) ? 'even' : 'odd'; ?>">
                            <?php echo link_to_item();?>
                        </li>   
                    <?php endwhile; ?>
                </ul>
                
                <p id="view-all-items"><a href="<?php echo html_escape(uri('items/browse')); ?>"><?php echo __('View All Items'); ?></a></p>
                <?php endif; ?>
            </div>
            
            <div id="tag-cloud" class="info-panel">
                <h2><?php echo __('Recent Tags'); ?></h2>
                <?php echo tag_cloud(recent_tags(), uri('items/browse/')); ?>
            </div>
            
            <?php fire_plugin_hook('admin_append_to_dashboard_secondary'); ?>
        </div>
<?php foot(); ?>
