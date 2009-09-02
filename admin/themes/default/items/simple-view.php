<table id="items" class="simple" cellspacing="0" cellpadding="0">
    <thead>
        <tr>
        <th scope="col">ID</th>
        <th scope="col">Title</th>
        <th scope="col">Type</th>
        <th scope="col">Creator</th>
        <th scope="col">Date Added</th>
        <th scope="col">Public</th>
        <th scope="col">Featured</th>
        <th scope="col">Edit?</th>
        </tr>
    </thead>
    <tbody>
<?php $key = 0; ?>

<?php while($item = loop_items()): ?>
<tr class="item<?php if(++$key%2==1) echo ' odd'; else echo ' even'; ?>">
    <td scope="row"><?php echo item('id');?>
    </td> 
    <td class="title"><?php echo link_to_item(); ?></td>
    <td><?php echo item('Item Type Name'); ?></td>
    <td><?php echo strip_formatting(item('Dublin Core', 'Creator')); ?></td>    
    <td><?php echo date('m.d.Y', strtotime(item('Date Added'))); ?></td>
    <td><?php 
    $publicCheckboxProps = array('name'=>"items[" . item('id') . "][public]",'class'=>"checkbox make-public");
    if (!has_permission('Items', 'makePublic')) {
       $publicCheckboxProps['disabled'] = 'disabled';
    }
    echo checkbox($publicCheckboxProps, item('Public')); ?></td>
    <td><?php 
    $featuredCheckboxProps = array('name'=>"items[" . item('id') . "][featured]",'class'=>"checkbox make-featured");
    if (!has_permission('Items', 'makeFeatured')) {
       $featuredCheckboxProps['disabled'] = 'disabled';
    }
    echo checkbox($featuredCheckboxProps, item('Featured')); ?>
        <?php echo hidden(array('name'=>"items[" . item('id') . "][id]"), item('id')); ?>
    </td>
    <td>
    <?php if (has_permission('Items', 'edit') or $item->wasAddedBy(current_user())): ?>
    <?php echo link_to_item('Edit', array('class'=>'edit'), 'edit'); ?>
    <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</tbody>
</table>



