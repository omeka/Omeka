<?php
echo head(array('title' => __('Settings'), 'bodyclass'=>'settings edit-search'));
echo common('settings-nav');
echo flash();
?>
<form method="post" class="search-record-types">
    <section class="seven columns alpha">
        <div class="field">
            <div class="two columns alpha">
                <label for="search_query_type"><?php echo __('Search Query Type') ?></label>
            </div>
            <div class="inputs five columns omega">
                <p class="explanation"><?php echo __('Select the default query type used for simple search in Omeka.'); ?></p>
                <?php echo $this->formRadio('search_query_type', $this->defaultQueryType, array(), $this->validQueryTypes); ?>
            </div>
        </div>
        <div class="field">
            <div class="two columns alpha">
                <label><?php echo __('Search Record Types'); ?></label>
            </div>
            <div class="inputs five columns omega">
                <fieldset>
                <legend class="explanation"><?php echo __('Customize which types of records '
                . 'will be searchable in Omeka.'); ?></legend>
                <?php foreach ($this->searchRecordTypes as $key => $value): ?>
                <label class="checkbox-label"><?php echo $this->formCheckbox("search_record_types[$key]", $key,
                array('checked' => array_key_exists($key, $this->customSearchRecordTypes))); ?> <?php echo $value; ?></label>
                <?php endforeach; ?>
                </fieldset>
            </div>
        </div>
        <div class="field">
            <div class="two columns alpha">
                <label><?php echo __('Index Records'); ?></label>
            </div>
            <div class="inputs five columns omega">
                <p class="explanation"><?php echo __('Click on the "Index Records" '
                . 'button to re-index your records.'); ?></p>
                <p class="explanation"><?php echo __('Indexing means to collect, parse, '
                . 'and store data to facilitate fast and accurate searches. Omeka will '
                . 'automatically index individual records as they are saved, but there '
                . 'are circumstances when your records are not indexed; for instance, '
                . 'when updating from an earlier version of Omeka or after you customize '
                . 'which records will be searchable.'); ?></p>
            </div>
        </div>
        <?php fire_plugin_hook('admin_settings_search_form', array('view' => $this)); ?>
    </section>
    <?php echo $csrf; ?>
    <section class="three columns omega">
        <div id="save" class="panel">
            <?php echo $this->formSubmit('submit_save_changes', __('Save Changes'), array('class'=>'submit full-width green button')); ?>
            <?php echo $this->formSubmit('submit_index_records', __('Index Records'), array('class'=>'submit full-width blue button')); ?>
        </div>
    </section>
</form>
<?php echo foot(); ?>
