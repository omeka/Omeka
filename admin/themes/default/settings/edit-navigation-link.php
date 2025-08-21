<?php
    $template = isset($template);
    $checkboxClasses = ['link-status'];
    if (!$template) {
        $checkboxId = 'main_nav_checkboxes_' . $pageCount;
        $checkboxValue = [
            'can_delete' => (bool) $page->can_delete,
            'uri' => $page->getHref(),
            'label' => $page->getLabel(),
            'visible' => $page->isVisible(),
        ];
        $checkboxChecked = $page->isVisible() ? 'checked="checked"' : '';
        if ($page->can_delete) {
            $checkboxClasses[] = 'can_delete_nav_link';
        }
    } else {
        $pageCount = '[pageId]';
        $checkboxId = 'navigation_main_nav_checkboxes_new_[pageId]';
        $checkboxValue = '';
        $checkboxChecked = 'checked="checked"';
        $checkboxClasses[] = 'can_delete_nav_link';
    }
    $checkboxClass = implode(' ', $checkboxClasses);
?>
<li <?php echo ($template) ? 'class="template"' : ''; ?>> 
    <div class="main_link">
        <div class="sortable-item drawer <?php echo ($template) ? 'opened' : ''; ?>">
            <span class="move icon" aria-label="<?php echo __('Move'); ?>" id="move-<?php echo $pageCount; ?>" aria-labelledby="move-<?php echo $pageCount; ?> drawer-<?php echo $pageCount; ?>" title="<?php echo __('Move'); ?>"></span>
            <?php if ($template): ?>
            <input type="hidden" class="new-link-hidden" value="0">
            <?php endif; ?>
            <input type="checkbox" name="<?php echo $checkboxId; ?>" id="<?php echo $checkboxId; ?>" value="<?php echo html_escape(json_encode($checkboxValue)); ?>" <?php echo $checkboxChecked; ?> class="<?php echo $checkboxClass; ?>" aria-label="<?php echo __('Enable'); ?>" aria-labelledby="<?php echo $checkboxId; ?> drawer-<?php echo $pageCount; ?>" title="<?php echo __('Enable'); ?>">
            <span class="drawer-name" id="drawer-<?php echo $pageCount; ?>">
            <?php echo (!$template) ? html_escape($page->getLabel()) : ''; ?>
            </span>
            <button type="button" id="drawer-toggle-<?php echo $pageCount; ?>" class="drawer-toggle" data-action-selector="opened" aria-expanded="<?php echo ($template) ? 'true' : 'false'; ?>" aria-controls="contents-<?php echo $pageCount; ?>" aria-label="<?php echo __('Show Options'); ?>" aria-label="<?php echo __('Show Options'); ?>" aria-labelledby="drawer-<?php echo $pageCount; ?> drawer-toggle-<?php echo $pageCount; ?>"><span class="icon"></span></button>
            <?php if ($template || $checkboxValue['can_delete']): ?>
            <button type="button" id="drawer-undo-<?php echo $pageCount; ?>" class="undo-delete" data-action-selector="deleted" aria-label="<?php echo __('Undo'); ?> <?php echo __('Remove'); ?>" aria-labelledby="drawer-undo-<?php echo $pageCount; ?> drawer-<?php echo $pageCount; ?>"  title="<?php echo __('Undo'); ?>"><span class="icon"></span></button>
            <button type="button" id="drawer-remove-<?php echo $pageCount; ?>" class="delete-drawer" data-action-selector="deleted" aria-label="<?php echo __('Remove'); ?>" aria-labelledby="drawer-remove-<?php echo $pageCount; ?> drawer-<?php echo $pageCount; ?>"  title="<?php echo __('Remove'); ?>"><span class="icon"></span></button>
            <?php endif; ?>
        </div>
        <div class="drawer-contents <?php echo ($template) ? 'opened' : ''; ?>" id="contents-<?php echo $pageCount; ?>">
            <label for="label-input-<?php echo $pageCount; ?>" class="label-label"><?php echo __('Label') ; ?></label><input type="text" id="label-input-<?php echo $pageCount; ?>" name="label-input-<?php echo $pageCount; ?>" class="navigation-label" />
            <label for="uri-input-<?php echo $pageCount; ?>" class="uri-label"><?php echo __('URL'); ?></label><input type="text" id="uri-input-<?php echo $pageCount; ?>" name="uri-input-<?php echo $pageCount; ?>" class="navigation-uri" />
            <div class="main_link_buttons"></div>
        </div>
    </div>
    <?php if (isset($page) && $page->hasChildren()): ?>
        <ul>
        <?php foreach ($page as $childPage): ?>
            <?php $pageCount += 10; ?>
            <?php echo $this->partial('settings/edit-navigation-link.php', [
                'page' => $childPage,
                'pageCount' => $pageCount
            ]);
            ?>
        <?php endforeach; ?>
        </ul>
<?php endif; ?>
</li>