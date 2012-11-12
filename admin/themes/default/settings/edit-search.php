<?php
$pageTitle = __('Settings');
echo head(array('title' => $pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'settings primary'));
?>
<?php echo common('settings-nav'); ?>
<?php echo flash(); ?>
<form method="post" class="search-record-types">
    <section class="seven columns alpha">
        <h2>Record Types</h2>
        <p>Here you may customize which types of records will be searchable in Omeka.</p>
        <ul>
            <?php foreach ($this->searchRecordTypes as $key => $value): ?>
            <li><?php echo $this->formCheckbox('search_record_types[]', $key, 
                           array('checked' => array_key_exists($key, $this->customSearchRecordTypes))); ?> <?php echo $value; ?></li>
            <?php endforeach; ?>
        </ul>
        <h2>Indexing</h2>
        <p>Indexing means to collect, parse, and store data to facilitate fast and 
        accurate searches. Omeka will automatically index individual records as they are 
        saved, but there are circumstances when your records are not indexed; for 
        instance, when updating from an earlier version of Omeka or after you customize 
        which records will be searchable.</p>
        <p>Click on the "Index Records" button to re-index your records.</p>
    </section>
    <section class="three columns omega">
        <div id="save" class="panel">
            <?php echo $this->formSubmit('submit_save_changes', __('Save Changes'), array('class'=>'submit big green button')); ?>
            <?php echo $this->formSubmit('submit_index_records', __('Index Records'), array('class'=>'submit big blue button')); ?>
        </div>
    </section>
</form>
<?php echo foot(); ?>
