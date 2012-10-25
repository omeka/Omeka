<?php
$pageTitle = __('Edit Elements');
echo head(array('title'=> $pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'element-sets primary'));
?>
<?php echo js_tag('elements'); ?>
<script type="text/javascript" charset="utf-8">
//<![CDATA[
jQuery(window).load(function () {
    Omeka.Elements.enableSorting();
});
//]]>
</script>
<?php echo common('settings-nav'); ?>
<?php echo flash(); ?>
<h2><?php echo __($element_set->name); ?></h2>
<p><?php echo __($element_set->description); ?></p>
<form method='post'>
<ul class="sortable">
    <?php foreach ($element_set->getElements() as $element): ?>
    <li class="element">
        <?php echo __($element->name); ?><br />
        <?php echo __($element->description); ?><br />
        <?php echo $this->formTextarea("elements[{$element->id}][comment]", $element->comment); ?>
        <?php echo $this->formHidden("elements[{$element->id}][order]", $element->order, array('class' => 'element-order')); ?>
    </li>
    <?php endforeach; ?>
</ul>
<?php echo $this->formSubmit('submit_edit_elements', 'Edit Elements'); ?>
</form>
<?php echo foot(); ?>
