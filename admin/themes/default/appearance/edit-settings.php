<?php
queue_js_file('sort-tiles', 'javascripts', [], filemtime(physical_path_to('javascripts/sort-tiles.js')));

$pageTitle = __('Appearance');
$sortTilesSpacingFix = '<style>.sort-tiles-widget + .sort-tiles-widget { margin-top: 30px; }</style>';
echo head(['title'=>$pageTitle, 'bodyclass'=>'settings']); ?>

<?php echo common('appearance-nav'); ?>

<?php echo flash(); ?>

<form id="appearance-form" method="post">

<section class="seven columns alpha">

    <?php echo $this->form; ?>

    <?php echo $sortTilesSpacingFix; ?>

    <?php echo $this->sortTilesWidget('items', __('Items Sort Options'), get_sort_tiles('items'), 'items_sort_options', get_option('items_sort_default_dir') ?: 'd', get_option('items_sort_default_field') ?: 'added'); ?>

    <?php echo $this->sortTilesWidget('collections', __('Collections Sort Options'), get_sort_tiles('collections'), 'collections_sort_options', get_option('collections_sort_default_dir') ?: 'd', get_option('collections_sort_default_field') ?: 'added'); ?>

    <?php fire_plugin_hook('admin_appearance_settings_form', ['form' => $form, 'view' => $this]); ?>

</section>

<section class="three columns omega">
    <div id="save" class="panel">
        <?php echo $this->formSubmit('appearance_submit', __('Save Changes'), ['class'=>'submit full-width green button']); ?>
    </div>
</section>

</form>

<script type="text/javascript">
    Omeka.SortTiles.removeText = <?php echo js_escape(__('Remove')); ?>;
    Omeka.SortTiles.moveText = <?php echo js_escape(__('Move')); ?>;
    Omeka.addReadyCallback(Omeka.SortTiles.enableSorting);
    Omeka.addReadyCallback(Omeka.SortTiles.enableTileRemoval);
    Omeka.addReadyCallback(Omeka.SortTiles.enableAddTile);
    Omeka.addReadyCallback(Omeka.SortTiles.setUpFormSubmission);
</script>

<?php echo foot(); ?>
