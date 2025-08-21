<?php
$doNotDelete = ['Dublin Core', 'Item Type Metadata'];

echo head(
    [
        'title' => __('Settings'),
        'bodyclass'=>'element-sets'
    ]
);
echo common('settings-nav');
echo flash();
?>
<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th><?php echo __('Name'); ?></th>
                <th><?php echo __('Description'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach (loop('element_sets') as $elementSet): ?>
            <?php if (ElementSet::ITEM_TYPE_NAME == $elementSet->name): continue; endif; ?>
            <tr>
                <td class="element-set-name">
                    <?php echo html_escape(__($elementSet->name)); ?>
                    <ul class="action-links">
                        <li><?php echo link_to($elementSet, 'edit', __('Edit')); ?></li>
                        <?php if (!in_array($elementSet->name, $doNotDelete)): ?>
                        <li><?php echo link_to($elementSet, 'delete-confirm', __('Delete'), ['class' => 'delete-confirm']); ?></li>
                        <?php endif; ?>
                    </ul>
                    <?php fire_plugin_hook('admin_element_sets_browse_each', ['element_set' => $elementSet, 'view' => $this]); ?>
                </td>
                <td>
                    <?php echo html_escape(__($elementSet->description)); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php fire_plugin_hook('admin_element_sets_browse', ['element_sets' => $element_sets, 'view' => $this]); ?>
<?php echo foot(); ?>
