<?php
    $pageCount++;
    $checkboxId = 'main_nav_checkboxes_' . $pageCount;
    $checkboxValue = array(
        'can_delete' => (bool) $page->can_delete,
        'uri' => $page->getHref(),
        'label' => $page->getLabel(),
        'visible' => $page->isVisible(),
    );
    $checkboxChecked = $page->isVisible() ? 'checked="checked"' : '';
    $checkboxClasses = array('link-status');
    if ($page->can_delete) {
        $checkboxClasses[] = 'can_delete_nav_link';
    }
    $checkboxClass = implode(' ', $checkboxClasses);
?>
<li>
    <div class="main_link">
        <div class="sortable-item drawer">
            <input type="checkbox" name="<?php echo $checkboxId; ?>" id="<?php echo $checkboxId; ?>" value="<?php echo html_escape(json_encode($checkboxValue)); ?>" <?php echo $checkboxChecked; ?> class="<?php echo $checkboxClass; ?>">
            <span class="drawer-name" id="drawer-<?php echo $checkboxId; ?>">
            <?php echo html_escape($page->getLabel()); ?>
            </span>
            <button type="button" id="drawer-toggle-<?php echo $checkboxId; ?>" class="drawer-toggle" data-action-selector="opened" aria-expanded="false" aria-label="' . __('Show Options') . '" aria-labelledby="drawer-<?php echo $checkboxId; ?> drawer-toggle-<?php echo $checkboxId; ?>"><span class="icon"></span></button>
            <?php if ($checkboxValue['can_delete']): ?>
            <button type="button" id="drawer-undo-<?php echo $checkboxId; ?>" class="undo-delete" data-action-selector="deleted" aria-label="<?php echo __('Undo'); ?> <?php echo __('Remove'); ?>" aria-labelledby="drawer-undo-<?php echo $checkboxId; ?> drawer-<?php echo $checkboxId; ?>"  title="<?php echo __('Undo'); ?>"><span class="icon"></span></button>
            <button type="button" id="drawer-remove-<?php echo $checkboxId; ?>" class="delete-drawer" data-action-selector="deleted" aria-label="<?php echo __('Remove'); ?>" aria-labelledby="drawer-remove-<?php echo $checkboxId; ?> drawer-<?php echo $checkboxId; ?>"  title="<?php echo __('Remove'); ?>"><span class="icon"></span></button>
            <?php endif; ?>
        </div>
        <div class="drawer-contents">
            <label><?php echo __('Label') ; ?></label><input type="text" class="navigation-label" />
            <label><?php echo __('URL'); ?></label><input type="text" class="navigation-uri" />
            <div class="main_link_buttons"></div>
        </div>
    </div>
    <?php if ($page->hasChildren()): ?>
        <ul>
        <?php foreach ($page as $childPage): ?>
            <?php echo $this->partial('settings/edit-navigation-link.php', [
                'page' => $childPage,
                'pageCount' => $pageCount
            ]);
            ?>
        <?php endforeach; ?>
        </ul>
<?php endif; ?>
</li>