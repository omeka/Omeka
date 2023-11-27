<?php
$pageTitle = __('Dashboard');
echo head(array('bodyclass'=>'index primary-secondary', 'title'=>$pageTitle)); ?>
    
<?php
$total_items = total_records('Item');
$total_collections = total_records('Collection');
$total_tags = total_records('Tag');
$stats = array(
    'items' => array($total_items, __(plural('item', 'items', $total_items))),
    'collections' => array($total_collections, __(plural('collection', 'collections', $total_collections))),
    'tags' => array($total_tags, __(plural('tag', 'tags', $total_tags)))
); ?>
<?php if (is_allowed('Plugins', 'edit')):
    $total_plugins = total_records('Plugin');
    $stats['plugins'] = array($total_plugins, __(plural('plugin', 'plugins', $total_plugins)));
endif; ?>
<?php if (is_allowed('Users', 'edit')):
    $total_users = total_records('User');
    $stats['users'] = array($total_users, __(plural('user', 'users', $total_users)));
endif; ?>
<?php if (is_allowed('Themes', 'edit')):
    $themeName = Theme::getTheme(Theme::getCurrentThemeName('public'))->title;
    $stats['themes'] = array($themeName, __('theme'));
endif; ?>
<?php $stats = apply_filters('admin_dashboard_stats', $stats, array('view' => $this)); ?>

<?php // Retrieve the latest version of Omeka by pinging the Omeka server. ?>
<?php $userRole = current_user()->role; ?>
<?php if ($userRole == 'super' || $userRole == 'admin'): ?>
<?php $latestVersion = latest_omeka_version(); ?>
      <?php if ($latestVersion and version_compare(OMEKA_VERSION, $latestVersion, '<')): ?>
            <div id="flash">
                <ul>
                    <li class="success"><?php echo __('A new version of Omeka is available for download.'); ?>
                    <a href="http://omeka.org/download/"><?php echo __('Upgrade to %s', $latestVersion); ?></a>
                    </li>
                </ul>
            </div>
      <?php endif; ?>
<?php endif; ?>

<section id="stats">
    <?php foreach ($stats as $statKey => $statInfo): ?>
    <p><?php echo link_to($statKey, null, '<span class="number">' . $statInfo[0] . '</span><br>' . $statInfo[1], array('class' => 'stat')); ?></p>
    <?php endforeach; ?>
</section>

<?php $panels = array(); ?>

<?php ob_start(); ?>
<h2><?php echo __('Recent Items'); ?></h2>
<?php
    set_loop_records('items', get_recent_items(5));
    foreach (loop('items') as $item):
?>
    <div class="recent-row">
        <p class="recent"><?php echo link_to_item(); ?></p>
        <?php if (is_allowed($item, 'edit')): ?>
        <p class="dash-edit"><?php echo link_to_item(__('Edit'), array(), 'edit'); ?></p>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
    <?php if (is_allowed('Items', 'add')): ?>
    <div class="add-new-link"><p><a class="add-new-item green button" href="<?php echo html_escape(url('items/add')); ?>"><?php echo __('Add a new item'); ?></a></p></div>
    <?php endif; ?>
<?php $panels[] = ob_get_clean(); ?>

<?php ob_start(); ?>
<h2><?php echo __('Recent Collections'); ?></h2>
<?php
    $collections = get_recent_collections(5);
    set_loop_records('collections', $collections);
    foreach (loop('collections') as $collection):
?>
    <div class="recent-row">
        <p class="recent"><?php echo link_to_collection() . " (" . metadata($collection, 'total_items') . ")"; ?></p>
        <?php if (is_allowed($collection, 'edit')): ?>
        <p class="dash-edit"><?php echo link_to_collection(__('Edit'), array(), 'edit'); ?></p>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
    <?php if (is_allowed('Collections', 'add')): ?>
    <div class="add-new-link"><p><a class="add-collection green button" href="<?php echo html_escape(url('collections/add')); ?>"><?php echo __('Add a new collection'); ?></a></p></div>
    <?php endif; ?>
<?php $panels[] = ob_get_clean(); ?>

<?php $panels = apply_filters('admin_dashboard_panels', $panels, array('view' => $this)); ?>
<div role="group" class="panels">
    <?php for ($i = 0; $i < count($panels); $i++): ?>
    <section class="panel five columns <?php echo ($i & 1) ? 'omega' : 'alpha'; ?>">
        <?php echo $panels[$i]; ?>
    </section>
    <?php endfor; ?>
    <?php fire_plugin_hook('admin_dashboard', array('view' => $this)); ?>
</div>
<?php echo foot(); ?>
