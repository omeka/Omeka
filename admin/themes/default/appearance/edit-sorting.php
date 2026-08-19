<?php
queue_js_file('sort-tiles', 'javascripts', [], filemtime(physical_path_to('javascripts/sort-tiles.js')));

$pageTitle = __('Appearance');
// Match the ~30px gap the rest of the page has between one legend section
// and the next; the sort-tiles fieldsets don't get it for free since they
// don't end in a Zend_Form .field row like the other display groups do.
$sortTilesSpacingFix = '<style>'
    . '.sort-tiles-widget + .sort-tiles-widget { margin-top: 30px; }'
    . '.add-tile-row .sortable-item { cursor: pointer; }'
    . '</style>';
echo head(['title'=>$pageTitle, 'bodyclass'=>'settings']); ?>

<?php echo common('appearance-nav'); ?>

<?php echo flash(); ?>

<form id="appearance-form" method="post">

<section class="seven columns alpha">

    <?php echo $sortTilesSpacingFix; ?>

    <?php echo $this->sortTilesWidget('items', __('Items Sort Options'), get_sort_tiles('items'), get_option('items_sort_default_dir') ?: 'd', get_option('items_sort_default_field') ?: 'added'); ?>

    <?php echo $this->sortTilesWidget('collections', __('Collections Sort Options'), get_sort_tiles('collections'), get_option('collections_sort_default_dir') ?: 'd', get_option('collections_sort_default_field') ?: 'added'); ?>

    <?php fire_plugin_hook('admin_appearance_sorting_form', ['form' => $form, 'view' => $this]); ?>

    <?php echo $this->form->getElement('sorting_csrf'); ?>

</section>

<section class="three columns omega">
    <div id="save" class="panel">
        <?php echo $this->formSubmit('sorting_submit', __('Save Changes'), ['class'=>'submit full-width green button']); ?>
    </div>
</section>

</form>

<script type="text/javascript">
    Omeka.SortTiles.addTileUrl = <?php echo js_escape(url('appearance/add-sort-tile')); ?>;
    Omeka.SortTiles.addTileErrorText = <?php echo js_escape(__('Unable to add the sort option.')); ?>;
    Omeka.addReadyCallback(Omeka.SortTiles.enableSorting);
    Omeka.addReadyCallback(Omeka.SortTiles.enableAddRowToggle);
    Omeka.addReadyCallback(Omeka.SortTiles.enableTileDeleteToggle);
    Omeka.addReadyCallback(Omeka.SortTiles.enableAddTile);
    Omeka.addReadyCallback(Omeka.SortTiles.setUpFormSubmission);
</script>

<?php echo foot(); ?>
