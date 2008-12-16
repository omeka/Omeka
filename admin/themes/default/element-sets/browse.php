<?php head(array('title'=>'Element Sets', 'content_class' => 'vertical-nav', 'bodyclass'=>'element-sets primary'));?>
<h1>Element Sets</h1>
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
                <?php echo htmlentities($elementSet->name); ?>
            </td>
            <td>
                <?php echo htmlentities($elementSet->description); ?>
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