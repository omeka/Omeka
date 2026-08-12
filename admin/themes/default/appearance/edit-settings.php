<?php
queue_js_file(['sort-tiles']);

$pageTitle = __('Appearance');
echo head(['title'=>$pageTitle, 'bodyclass'=>'settings']); ?>

<?php echo common('appearance-nav'); ?>

<?php echo flash(); ?>

<form id="appearance-form" method="post">

<section class="seven columns alpha">

    <?php echo $this->form; ?>

    <h2><?php echo __('Items Sort Options'); ?></h2>
    <?php echo $this->sortTilesWidget('items', get_sort_tiles('items'), 'items_sort_options'); ?>

    <h2><?php echo __('Collections Sort Options'); ?></h2>
    <?php echo $this->sortTilesWidget('collections', get_sort_tiles('collections'), 'collections_sort_options'); ?>

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
    Omeka.addReadyCallback(Omeka.SortTiles.enableSorting);
    Omeka.addReadyCallback(Omeka.SortTiles.enableTileRemoval);
    Omeka.addReadyCallback(Omeka.SortTiles.enableAddTile);
    Omeka.addReadyCallback(Omeka.SortTiles.setUpFormSubmission);
</script>

<?php echo foot(); ?>
