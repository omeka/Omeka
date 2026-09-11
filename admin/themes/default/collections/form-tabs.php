<?php
 
$tabs = [];
foreach ($elementSets as $key => $elementSet) {
    $tabName = $elementSet->name;
    $tabContent  = '<p class="element-set-description" id="';
    $tabContent .= html_escape(text_to_id($elementSet->name) . '-description') . '">';
    $tabContent .= url_to_link(__($elementSet->description)) . '</p>' . "\n\n";
    $tabContent .= element_set_form($collection, $elementSet->name);    
    $tabs[$tabName] = $tabContent;    
}

$tabs = apply_filters('admin_collections_form_tabs', $tabs, ['collection' => $collection]);
?>

<!-- Create the sections for the various element sets -->
<ul id="section-tabs" aria-label="<?php echo __('Form'); ?>" class="tabs navigation" role="tablist">
    <?php foreach ($tabs as $tabName => $tabContent): ?>
        <?php if (!empty($tabContent)): // Don't display tabs with no content. '?>
            <li><button type="button" role="tab" aria-selected="false" aria-controls="<?php echo html_escape(text_to_id($tabName) . '-metadata'); ?>"><?php echo html_escape(__($tabName)); ?></button></li>
        <?php endif; ?> 
    <?php endforeach; ?>
</ul>
