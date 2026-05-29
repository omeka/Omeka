<?php
$successAlertTemplate = __("<span class='nav-item-title'></span> reordered. ");
$failAlertTemplate = __("Cannot reorder further.");
$upActionAlertTemplate = __("Moved above <span class='positional-nav-item-title'></span>.");
$downActionAlertTemplate = __("Moved below <span class='positional-nav-item-title'></span>.");
?>
<?php if (metadata('item', 'has files')): ?>
    <p class="explanation"><?php echo __('Click and drag the files into the preferred display order.'); ?></p>
    <div id="file-list">
        <ul class="sortable">
        <?php foreach( $item->Files as $key => $file ): ?>
            <li class="file">
                <?php $fileId = $file->id; ?>
                <div class="sortable-item drawer">
                    <span id="move-<?php echo $fileId; ?>" class="move icon" title="<?php echo __('Move'); ?>" aria-label="<?php echo __('Move'); ?>" aria-labelledby="move-<?php echo $fileId; ?> file-<?php echo $fileId; ?>"></span>
                    <?php echo file_image('square_thumbnail', [], $file); ?>
                    <?php echo link_to($file, 'show', html_escape($file->original_filename), ['class' => 'drawer-name', 'id' => 'file-' . $fileId]); ?>
                    <?php echo $this->formHidden("order[{$file->id}]", $file->order, ['class' => 'file-order']); ?>
                    <div class="keyboard-reorder-group">
                        <button type="button" class="keyboard-reorder" aria-label="<?php echo __('Reorder with keyboard'); ?>" title="<?php echo __('Reorder with keyboard'); ?>" aria-expanded="false" aria-controls="keyboard-reorder-<?php echo $elementId; ?>"></button>
                        <div class="keyboard-reorder-panel" id="keyboard-reorder-<?php echo $elementId; ?>" role="group" aria-label="<?php echo __('Reorder actions'); ?>">
                            <button type="button" class="keyboard-reorder-up" aria-label="<?php echo __('Move up'); ?>" title="<?php echo __('Move up'); ?>"></button>
                            <button type="button" class="keyboard-reorder-down" aria-label="<?php echo __('Move down'); ?>" title="<?php echo __('Move down'); ?>"></button>
                        </div>
                    </div>
                    <?php echo link_to($file, 'edit', __('Edit'), ['class'=>'edit', 'title' => __('Edit')]); ?>
                    <button type="button" id="remove-file-<?php echo $fileId; ?>" class="delete-drawer" data-action-selector="deleted" title="<?php echo __('Remove'); ?>" aria-label="<?php echo __('Remove'); ?>" aria-labelledby="<?php echo 'file-' . $fileId; ?> remove-file-<?php echo $fileId; ?>-name"><span class="icon" aria-hidden="true"></span></button>
                    <button type="button" id="return-file-<?php echo $fileId; ?>" class="undo-delete" data-action-selector="deleted" title="<?php echo __('Undo'); ?>" aria-label="<?php echo __('Undo'); ?> <?php echo __('Remove'); ?>" aria-labelledby="<?php echo 'file-' . $fileId; ?> return-file-<?php echo $fileId; ?>"><span class="icon" aria-hidden="true"></span></button>
                    <?php echo $this->formCheckbox('delete_files[]', $fileId, ['checked' => false, 'id' => 'delete_files_' . $fileId, 'class' => 'delete-checkbox']); ?>
                </div>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="add-new"><?php echo __('Add New Files'); ?></div>
<div class="drawer-contents opened">
    <p><?php echo __('The maximum file size is %s.', max_file_size()); ?></p>

    <div id="file-inputs">
        <label><?php echo __('Find a File'); ?></label>
        <button type="button" id="add-file" class="add-file button"><?php echo __('Add Another File'); ?></button>
    </div>

    <?php
    $fileTemplate = <<<FILE_TEMPLATE
    <div class="file-container">
        <input name="file[__INDEX__]" type="file" class="file-input" multiple>
        <div class="file-info">
            <div class="file-thumbnail"></div>
            <div class="file-size"></div>
        </div>
    </div>
FILE_TEMPLATE;
    ?>
    <div class="files" data-file-container-template="<?php echo utf8_htmlspecialchars($fileTemplate); ?>"></div>
</div>

<?php fire_plugin_hook('admin_items_form_files', ['item' => $item, 'view' => $this]); ?>
