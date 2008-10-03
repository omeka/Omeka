<?php 
$tabs = array();
foreach ($elementSets as $key => $elementSet) {
    $tabName = $elementSet->name;
    switch ($tabName) {
        case ELEMENT_SET_ITEM_TYPE:
            // Output buffer this form instead of displaying it right away.
            ob_start();
            include 'item-type-form.php';
            $tabs[$tabName] = ob_get_contents();
            ob_end_clean();
            break;
        
        default:
            $tabs[$tabName] = display_element_set_form_for_item($item, $elementSet->name);
            break;
    }
}
foreach (array('Collection', 'Files', 'Tags', 'Miscellaneous') as $tabName) {
    ob_start();
    switch ($tabName) {
        case 'Collection':
            require 'collection-form.php';
        break;
        case 'Files':
            require 'files-form.php';
        break;
        case 'Tags':
            require 'tag-form.php';
        break;
        case 'Miscellaneous':
            require 'miscellaneous-form.php';
        break;
    }
    $tabs[$tabName] = ob_get_contents();
    ob_end_clean();
} ?>

* <!-- Create the sections for the various element sets -->

<ul id="section-nav" class="navigation tabs">
    <?php foreach ($tabs as $tabName => $tabContent): ?>
        <?php if (!empty($tabContent)): // Don't display tabs with no content. '?>
            <li><a href="#<?php echo text_to_id($tabName);?>-metadata"><?php echo $tabName; ?></a></li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>