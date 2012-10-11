<?php
 
$tabs = array();
foreach ($elementSets as $key => $elementSet) {
    $tabName = $elementSet->name;
    $tabContent  = '<p class="element-set-description" id="';
    $tabContent .= html_escape(text_to_id($elementSet->name) . '-description') . '">';
    $tabContent .= url_to_link($elementSet->description) . '</p>' . "\n\n";
    $tabContent .= element_set_form($collection, $elementSet->name);    
    $tabs[$tabName] = $tabContent;    
}

foreach (array('Collectors') as $tabName) {
    ob_start();
    switch ($tabName) {
        case 'Collectors':
            require 'collectors-form.php';
        break;
    }
    $tabs[$tabName] = ob_get_contents();
    ob_end_clean();
}

$tabs = apply_filters('admin_collections_form_tabs', $tabs, array('collection' => $collection));
?>

<!-- Create the sections for the various element sets -->

<ul id="section-nav" class="navigation tabs">
    <?php foreach ($tabs as $tabName => $tabContent): ?>
        <?php if (!empty($tabContent)): // Don't display tabs with no content. '?>
            <li><a href="#<?php echo html_escape(text_to_id($tabName) . '-metadata'); ?>"><?php echo html_escape(__($tabName)); ?></a></li>
        <?php endif; ?> 
    <?php endforeach; ?>
</ul>