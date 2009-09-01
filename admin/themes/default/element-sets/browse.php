<?php head(array('title'=>'Browse Element Sets', 'content_class' => 'vertical-nav', 'bodyclass'=>'element-sets primary'));?>
<h1>Browse Element Sets (<?php echo count($elementsets) ?> total)</h1>
<?php common('settings-nav'); ?>

<div id="primary">

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Delete</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($elementsets as $elementSet): ?>
        <tr>
            <?php $doNotDelete = array('Dublin Core', 'Item Type Metadata', 'Omeka Image File', 'Omeka Video File'); ?>
            
            <td width="30%">
                <?php echo html_escape($elementSet->name); ?>
            </td>
            <td>
                <?php echo html_escape($elementSet->description); ?>
            </td>
            <td>
                <?php if (has_permission('ElementSets', 'delete') and !in_array($elementSet->name, $doNotDelete)): ?>
                    <?php echo link_to($elementSet, 'delete', 'Delete', array('class'=>'delete')); ?>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

</div>

<?php foot(); ?>