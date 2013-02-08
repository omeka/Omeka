<?php
$pageTitle = __('Dashboard');
echo head(array('bodyclass'=>'index primary-secondary', 'title'=>$pageTitle)); ?>
    
<?php $stats = array(
    array(link_to('items', null, total_records('Item')), __('items')),
    array(link_to('collections', null, total_records('Collection')), __('collections')),
    array(link_to('tags', null, total_records('Tag')), __('tags'))
); ?>
<?php if (is_allowed('Plugins', 'edit')):
    $stats[] = array(link_to('plugins', null, total_records('Plugin')), __('plugins'));
endif; ?>
<?php if (is_allowed('Users', 'edit')):
    $stats[] = array(link_to('users', null, total_records('User')), __('users'));
endif; ?>
<?php if (is_allowed('Themes', 'edit')):
    $themeName = Theme::getTheme(Theme::getCurrentThemeName('public'))->title;
    $stats[] = array(link_to('themes', null, $themeName), __('theme'));
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
    <?php foreach ($stats as $statInfo): ?>
    <p><span class="number"><?php echo $statInfo[0]; ?></span><br><?php echo $statInfo[1]; ?></p>
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
    <div class="add-new-link"><p><a class="add-new" href="<?php echo html_escape(url('items/add')); ?>"><?php echo __('Add a new item'); ?></a></p></div>
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
        <p class="recent"><?php echo link_to_collection(); ?></p>
        <?php if (is_allowed($collection, 'edit')): ?>
        <p class="dash-edit"><?php echo link_to_collection(__('Edit'), array(), 'edit'); ?></p>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
    <?php if (is_allowed('Collections', 'add')): ?>
    <div class="add-new-link"><p><a class="add-collection" href="<?php echo html_escape(url('collections/add')); ?>"><?php echo __('Add a new collection'); ?></a></p></div>
    <?php endif; ?>
<?php $panels[] = ob_get_clean(); ?>

<?php $panels = apply_filters('admin_dashboard_panels', $panels, array('view' => $this)); ?>
<?php for ($i = 0; $i < count($panels); $i++): ?>
<section class="five columns <?php echo ($i & 1) ? 'omega' : 'alpha'; ?>">
    <div class="panel">
        <?php echo $panels[$i]; ?>
    </div>
</section>
<?php endfor; ?>

<?php fire_plugin_hook('admin_dashboard', array('view' => $this)); ?>

<?php echo foot(); ?>
