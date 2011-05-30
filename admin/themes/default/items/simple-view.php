<table id="items" class="simple" cellspacing="0" cellpadding="0">
    <thead>
        <tr>
        <?php echo browse_headings(array(
            __('ID') => 'id',
            __('Title') => 'Dublin Core,Title',
            __('Type') => null,
            __('Creator') => 'Dublin Core,Creator',
            __('Date Added') => 'added',
            __('Public') => 'public',
            __('Featured') => 'featured',
            __('Edit') => null)); ?>
        </tr>
    </thead>
    <tbody>
<?php $key = 0; ?>

<?php while($item = loop_items()): ?>
<tr class="item <?php if(++$key%2==1) echo 'odd'; else echo 'even'; ?>">
    <?php $id = item('id'); ?>
    <td scope="row"><?php echo $id; ?>
    </td> 
    <td class="title">
        <?php echo link_to_item(); ?>
        <?php fire_plugin_hook('admin_append_to_items_browse_simple_each'); ?>
    </td>
    <td><?php echo ($typeName = item('Item Type Name'))
                 ? $typeName
                 : '<em>' . item('Dublin Core', 'Type', array('snippet' => 35)) . '</em>'; ?></td>
    <td><?php echo strip_formatting(item('Dublin Core', 'Creator')); ?></td>    
    <td><?php echo date('m.d.Y', strtotime(item('Date Added'))); ?></td>
    <td><?php 
    $publicCheckboxProps = array('name'=>"items[$id][public]",'class'=>"checkbox make-public");
    if (!has_permission('Items', 'makePublic')) {
       $publicCheckboxProps['disabled'] = 'disabled';
    }
    echo checkbox($publicCheckboxProps, item('Public')); ?></td>
    <td><?php 
    $featuredCheckboxProps = array('name'=>"items[$id][featured]",'class'=>"checkbox make-featured");
    if (!has_permission('Items', 'makeFeatured')) {
       $featuredCheckboxProps['disabled'] = 'disabled';
    }
    echo checkbox($featuredCheckboxProps, item('Featured')); ?>
        <?php echo hidden(array('name'=>"items[$id][id]"), $id); ?>
    </td>
    <td>
    <?php if (has_permission($item, 'edit')): ?>
    <?php echo link_to_item(__('Edit'), array('class'=>'edit'), 'edit'); ?>
    <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</tbody>
</table>



