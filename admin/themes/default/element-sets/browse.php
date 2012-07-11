<?php 
$pageTitle = __('Browse Element Sets');
head(array('title'=> $pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'element-sets primary'));
$doNotDelete = array('Dublin Core', 'Item Type Metadata', 'Omeka Image File', 'Omeka Video File');
?>
<?php common('settings-nav'); ?>
<?php echo flash(); ?>
<table>
    <thead>
        <tr>
            <th><?php echo __('Name'); ?></th>
            <th><?php echo __('Description'); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($elementsets as $elementSet): ?>
        <tr>
            <td class="element-set-name">
                <?php echo html_escape(__($elementSet->name)); ?>
                <ul class="action-links">
                    <?php if (ELEMENT_SET_ITEM_TYPE != $elementSet->name): ?>
                    <li><?php echo link_to($elementSet, 'edit', __('Edit')); ?></li>
                    <?php endif; ?>
                    <?php if (!in_array($elementSet->name, $doNotDelete)): ?>
                    <li><?php echo link_to($elementSet, 'delete-confirm', __('Delete'), array('class' => 'delete')); ?></li>
                    <?php endif; ?>
                </ul>
            </td>
            <td>
                <?php echo html_escape(__($elementSet->description)); ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

</div>

<?php foot(); ?>
