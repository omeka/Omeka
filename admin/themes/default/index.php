<?php
$pageTitle = __('Dashboard');
head(array('bodyclass'=>'index primary-secondary', 'title'=>$pageTitle)); ?>
    
            <section id="stats">
            
                <?php 
                    $db = get_db();
                    $pluginRecords = $db->getTable('Plugin')->count();
                    $themeName = Theme::getAvailable(Theme::getCurrentThemeName('public'))->title;
                ?>
                <p><span class="number"><?php echo link_to('items', null, __(total_items())) ?></span><br /><?php echo __('items') ?></p>
                <p><span class="number"><?php echo link_to('collections', null, __(total_collections())); ?></span><br /><?php echo __('collections') ?></p>
                <?php if(has_permission('Plugins','edit')): ?>
                <p><span class="number"><?php echo link_to('plugins', null, __($pluginRecords)); ?></span><br /><?php echo __('plugins') ?></p>
                <?php endif; ?>
                <p><span class="number"><?php echo link_to('tags', null, __(total_tags())); ?></span><br /><?php echo __('tags') ?></p>
                <?php if(has_permission('Users','edit')): ?>
                <p><span class="number"><?php echo link_to('users', null, __(total_users())); ?></span><br /><?php echo __('users') ?></p>
                <?php endif; ?>
                <p>
                    <span class="number">
                    <?php if (get_option('display_system_info') && has_permission('SystemInfo', 'index')): ?>
                    <a href="<?php echo html_escape(uri('system-info')); ?>" ><?php echo OMEKA_VERSION; ?></a>
                    <?php else: ?>
                    <?php echo OMEKA_VERSION; ?>
                    <?php endif; ?>
                    </span><br /><?php echo __('Omeka version'); ?>
                </p>
                <?php if(has_permission('Themes','edit')): ?>                
                <p class="theme"><span class="number"><?php echo link_to('themes', null, $themeName); ?></a></span><br />theme</p>
                <?php endif; ?>
            </section>
            
            <section id="recent-collections" class="five columns alpha">
                <div class="panel">
                    <h2 class="serif">Recent Collections</h2>
                    <?php
                        
                        $collections = get_collections(array('recent'=>true),5);
                        set_collections_for_loop($collections);
                        
                        while($collection = loop_collections()):
                            echo '<div class="recent-row">';
                            echo '<p class="recent">'.link_to_collection().'</p>';
                            if (has_permission($collection, 'edit')):
                            echo '<p class="dash-edit">'.link_to_collection(__('Edit'), array('class'=>'dash-edit'), 'edit').'</p>';
                            endif;                        
                            echo '</div>';
                        endwhile;
                        
                       ?>
                    <div class="add-new"><p><a class="add-collection" href="<?php echo html_escape(uri('collections/add')); ?>">Add a new collection</a></p></div>
                </div>
            </section>
            
            <section id="recent-items" class="five columns omega">
                <div class="panel">
                <h2 class="serif">Recent Items</h2>
                    <?php 
                     
                        $items = recent_items(5); 
                        set_items_for_loop($items);
                     
                        while($item = loop_items()):
                            echo '<div class="recent-row">';
                            echo '<p class="recent">'.link_to_item().'</p>';
                            if (has_permission($item, 'edit')):
                            echo '<p class="dash-edit">'.link_to_item(__('Edit'), array(), 'edit').'</p>';
                            endif;
                            echo '</div>';                            
                        endwhile;
                     
                    ?>
                <div class="add-new"><p><a class="add-new" href="<?php echo html_escape(uri('items/add')); ?>">Add a new item</a></p></div>
                </div>
            </section>
    
<?php foot(); ?>
