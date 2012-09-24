<?php
$pageTitle = __('Dashboard');
echo head(array('bodyclass'=>'index primary-secondary', 'title'=>$pageTitle)); ?>
    
            <section id="stats">
            
                <?php
                    $themeName = Theme::getTheme(Theme::getCurrentThemeName('public'))->title;
                ?>
                <p><span class="number"><?php echo link_to('items', null, __(total_records('Item'))) ?></span><br /><?php echo __('items') ?></p>
                <p><span class="number"><?php echo link_to('collections', null, __(total_records('Collection'))); ?></span><br /><?php echo __('collections') ?></p>
                <?php if(has_permission('Plugins','edit')): ?>
                <p><span class="number"><?php echo link_to('plugins', null, __(total_records('Plugin'))); ?></span><br /><?php echo __('plugins') ?></p>
                <?php endif; ?>
                <p><span class="number"><?php echo link_to('tags', null, __(total_records('Tag'))); ?></span><br /><?php echo __('tags') ?></p>
                <?php if(has_permission('Users','edit')): ?>
                <p><span class="number"><?php echo link_to('users', null, __(total_records('User'))); ?></span><br /><?php echo __('users') ?></p>
                <?php endif; ?>
                <p>
                    <span class="number">
                    <?php if (get_option('display_system_info') && has_permission('SystemInfo', 'index')): ?>
                    <a href="<?php echo html_escape(url('system-info')); ?>" ><?php echo OMEKA_VERSION; ?></a>
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
                        
                        $collections = get_recent_collections(5);
                        set_loop_records('collections', $collections);
                        
                        foreach (loop('collections') as $collection):
                            echo '<div class="recent-row">';
                            echo '<p class="recent">'.link_to_collection().'</p>';
                            if (has_permission($collection, 'edit')):
                            echo '<p class="dash-edit">'.link_to_collection(__('Edit'), array('class'=>'dash-edit'), 'edit').'</p>';
                            endif;                        
                            echo '</div>';
                        endforeach;
                        
                       ?>
                    <div class="add-new"><p><a class="add-collection" href="<?php echo html_escape(url('collections/add')); ?>">Add a new collection</a></p></div>
                </div>
            </section>
            
            <section id="recent-items" class="five columns omega">
                <div class="panel">
                <h2 class="serif">Recent Items</h2>
                    <?php 
                     
                        $items = get_recent_items(5); 
                        set_loop_records('items', $items);
                     
                        foreach (loop('items') as $item):
                            echo '<div class="recent-row">';
                            echo '<p class="recent">'.link_to_item().'</p>';
                            if (has_permission($item, 'edit')):
                            echo '<p class="dash-edit">'.link_to_item(__('Edit'), array(), 'edit').'</p>';
                            endif;
                            echo '</div>';                            
                        endforeach;
                     
                    ?>
                <div class="add-new"><p><a class="add-new" href="<?php echo html_escape(url('items/add')); ?>">Add a new item</a></p></div>
                </div>
            </section>
    
<?php echo foot(); ?>
