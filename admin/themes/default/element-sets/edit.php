<?php
$pageTitle = __('Edit Elements');
echo head(array('title'=> $pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'element-sets primary'));
?>
<?php echo js_tag('elements'); ?>
<script type="text/javascript" charset="utf-8">
//<![CDATA[
jQuery(window).load(function () {
    Omeka.Elements.enableSorting();
    Omeka.Elements.addHideButtons();
});
//]]>
</script>
<?php echo common('settings-nav'); ?>
<?php echo flash(); ?>
<form method='post'>

<section class="seven columns alpha">
    <h2><?php echo __($element_set->name); ?></h2>
    <p><?php echo __($element_set->description); ?></p>
    <ul class="sortable">
        <?php foreach ($element_set->getElements() as $element): ?>
        <li class="element">
            <div class="sortable-item">
                <?php echo __($element->name); ?>
            </div>
            <div class="drawer-contents">
                <p><?php echo __($element->description); ?></p>
                <label for="<?php echo "elements[{" . $element->id . "}][comment]"; ?>"><?php echo __('Comment'); ?></label>
                <?php echo $this->formTextarea("elements[{$element->id}][comment]", $element->comment); ?>
                <?php echo $this->formHidden("elements[{$element->id}][order]", $element->order, array('class' => 'element-order')); ?>
            </div>
        </li>
        <?php endforeach; ?>
</ul>
</section>

<section class="three columns omega">
    <div id="save" class="panel">
        <?php echo $this->formSubmit('submit_edit_elements', 'Save Changes', array('class' => 'big green button')); ?>
    </div>
</section>

</form>
</div>
<?php echo foot(); ?>
