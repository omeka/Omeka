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
            $tabContent  = '<span class="element-set-description" id="';
            $tabContent .= html_escape(text_to_id($elementSet->name) . '-description') . '">';            
            $tabContent .= url_to_link($elementSet->description) . '</span>' . "\n\n";
            $tabContent .= display_element_set_form($item, $elementSet->name);
            $tabs[$tabName] = $tabContent;
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
} 

$tabs = apply_filters('admin_items_form_tabs', $tabs, $item);
?>

<!-- Create the sections for the various element sets -->

<ul id="section-nav" class="navigation tabs">
    <?php foreach ($tabs as $tabName => $tabContent): ?>
        <?php if (!empty($tabContent)): // Don't display tabs with no content. '?>
            <li><a href="#<?php echo html_escape(text_to_id($tabName) . '-metadata'); ?>"><?php echo html_escape(__($tabName)); ?></a></li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>