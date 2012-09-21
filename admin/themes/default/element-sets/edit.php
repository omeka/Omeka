<?php
$pageTitle = __('Edit Elements');
head(array('title'=> $pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'element-sets primary'));
?>
<?php common('settings-nav'); ?>
<?php echo flash(); ?>
<h2><?php echo __($element_set->name); ?></h2>
<p><?php echo __($element_set->description); ?></p>
<form method='post'>
    <table>
        <thead>
        <tr>
            <th><?php echo __('Name'); ?></th>
            <th><?php echo __('Description'); ?></th>
            <th><?php echo __('Comment'); ?></th>
            <th><?php echo __('Order'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($element_set->getElements() as $element): ?>
        <tr>
            <td><?php echo __($element->name); ?></td>
            <td><?php echo __($element->description); ?></td>
            <td><?php echo $this->formTextarea("elements[{$element->id}][comment]", $element->comment, array('rows' => '5', 'cols' => '40')); ?></td>
            <td><?php echo $this->formText("elements[{$element->id}][order]", $element->order, array('size' => '3')); ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php echo $this->formSubmit('submit_edit_elements', 'Edit Elements'); ?>
</form>
<?php foot(); ?>
