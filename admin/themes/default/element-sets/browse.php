<?php 
$pageTitle = __('Browse Element Sets');
head(array('title'=> $pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'element-sets primary'));?>
<h1><?php echo $pageTitle; ?> <?php echo __('(%s total)', $total_records); ?></h1>
<?php common('settings-nav'); ?>

<div id="primary">

<table>
    <thead>
        <tr>
            <th><?php echo __('Name'); ?></th>
            <th><?php echo __('Description'); ?></th>
            <th><?php echo __('Delete'); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($elementsets as $elementSet): ?>
        <tr>
            <?php $doNotDelete = array('Dublin Core', 'Item Type Metadata', 'Omeka Image File', 'Omeka Video File'); ?>
            
            <td class="element-set-name">
                <?php echo html_escape(__($elementSet->name)); ?>
            </td>
            <td>
                <?php echo html_escape(__($elementSet->description)); ?>
            </td>
            <td>
                <?php if (has_permission('ElementSets', 'delete') and !in_array($elementSet->name, $doNotDelete)): ?>
                    <?php echo delete_button($elementSet); ?>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

</div>

<?php foot(); ?>
