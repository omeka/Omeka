<?php 
$pageTitle = __('Browse Element Sets');
head(array('title'=> $pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'element-sets primary'));?>

<?php common('settings-nav'); ?>

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
            <?php $doNotDelete = array('Dublin Core', 'Item Type Metadata', 'Omeka Image File', 'Omeka Video File'); ?>
            
            <td class="element-set-name">
                <?php echo html_escape(__($elementSet->name)); ?>
                <?php if (has_permission('ElementSets', 'delete') and !in_array($elementSet->name, $doNotDelete)): ?>
                <ul class="action-links">
                    <li><?php echo link_to($elementSet, 'delete-confirm', __('Delete'), array('class' => 'delete')); ?></li>
                </ul>
                <?php endif; ?>
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
