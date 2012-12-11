<?php 
echo head(array('bodyclass' => 'error404', 'title' => __('404: Page Not Found')));
echo flash();
?>

<p><?php echo __('You&#8217;ve tried to access a page that does not seem to exist. Sometimes this happens. Below is a quick break-down of the items, collections, and exhibits on this site.'); ?></p>
<p>
    <?php 
    $siteAdministratorEmail = '<a href="mailto:'. option('administrator_email') . '">' . __('site administrator') . '</a>';
    echo __('If this does not help, try contacting the %s.', $siteAdministratorEmail);
    ?>
</p>

<section class="five columns alpha">
    <div class="panel">
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
        <div class="add-new-link"><p><a class="add-new-link" href="<?php echo html_escape(url('items/add')); ?>"><?php echo __('Add a new item'); ?></a></p></div>
        <?php endif; ?>
    </div>
</section>

<section class="five columns omega">
    <div class="panel">
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
    </div>
</section>

<?php echo foot(); ?>
