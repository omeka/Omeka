<?php 
$pathToConvert = get_option('path_to_convert');
if (empty($pathToConvert) && is_allowed('Settings', 'edit')): ?>
    <div class="error"><?php echo __('The path to Image Magick has not been set. No derivative images will be created. If you would like Omeka to create derivative images, please add the path to your settings form.'); ?></div>
<?php endif; ?>
<?php if ( item_has_files() ): ?>
    <h3><?php echo __('Current Files'); ?></h3>
    <div id="file-list">
    <table>
        <thead>
            <tr>
                <th><?php echo __('File Name'); ?></th>
                <th><?php echo __('Edit File Metadata'); ?></th>
                <th><?php echo __('Order'); ?></th>
                <th><?php echo __('Delete'); ?></th>
            </tr>
        </thead>
        <tbody>
    <?php foreach( $item->Files as $key => $file ): ?>
        <tr>
            <td><?php echo link_to($file, 'show', html_escape($file->original_filename), array()); ?></td>
            <td class="file-link">
                <?php echo link_to($file, 'edit', __('Edit'), array('class'=>'edit')); ?>
            </td>
            <td><?php echo $this->formText("order[{$file->id}]", $file->order, array('size' => 3)); ?></td>
            <td class="delete-link">
                <?php echo $this->formCheckbox('delete_files[]', $file->id, array('checked' => false)); ?>
            </td>   
        </tr>

    <?php endforeach; ?>
    </tbody>
    </table>
    </div>
<?php endif; ?>
<h3><?php echo __('Add New Files'); ?></h3>

<div id="add-more-files">
<label for="add_num_files"><?php echo __('Find a File'); ?></label>
    <div class="files">
    <?php $numFiles = (int)@$_REQUEST['add_num_files'] or $numFiles = 1; ?>
    <?php 
    echo $this->formText('add_num_files', $numFiles, array('size' => 2));
    echo $this->formSubmit('add_more_files', 'Add this many files'); 
    ?>
    </div>
</div>

<div class="field two columns alpha" id="file-inputs">
    <label><?php echo __('Find a File'); ?></label>
</div>

<?php for($i=0;$i<$numFiles;$i++): ?>
<div class="files inputs five columns omega">
    <input name="file[<?php echo $i; ?>]" id="file-<?php echo $i; ?>" type="file" class="fileinput" />          
</div>
<?php endfor; ?>


<?php fire_plugin_hook('admin_append_to_items_form_files', array('item' => $item, 'view' => $this)); ?>
