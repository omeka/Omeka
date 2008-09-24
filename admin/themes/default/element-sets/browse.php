<?php head(array('title'=>'Element Sets', 'content_class' => 'vertical-nav', 'body_class'=>'element-sets primary'));?>
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
            <td width="30%">
                <?php echo htmlentities($elementSet->name); ?>
            </td>
            <td>
                <?php echo htmlentities($elementSet->description); ?>
            </td>
            <td><?php echo link_to($elementSet, 'delete', 'Delete', array('class'=>'delete')); ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

</div>

<?php foot(); ?>