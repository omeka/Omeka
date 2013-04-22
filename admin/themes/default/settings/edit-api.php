<?php
echo head(array('title' => __('Api'), 'bodyclass'=>'settings edit-api'));
echo common('settings-nav');
echo flash();
?>
<form method="post">
    <section class="seven columns alpha">
        <div class="field">
            <div class="two columns alpha">
                <label for="search_record_types"><?php echo __('Enable API'); ?></label>
            </div>
            <div class="inputs five columns omega">
                <p class="explanation"><?php echo __(
                    'Enable or disable the API by checking or uncheking the box below.'
                ); ?></p>
                <?php echo $this->formCheckbox('api_enable', null, array('checked' => (bool) get_option('api_enable'))); ?>
            </div>
        </div>
        <div class="field">
            <div class="two columns alpha">
                <label for="search_record_types"><?php echo __('Results per Page'); ?></label>
            </div>
            <div class="inputs five columns omega">
                <p class="explanation"><?php echo __(
                    'Limit the number of API results displayed per page.'
                ); ?></p>
                <?php echo $this->formText('api_per_page', get_option('api_per_page')); ?>
            </div>
        </div>
        <?php fire_plugin_hook('admin_settings_api_form', array('view' => $this)); ?>
    </section>
    <section class="three columns omega">
        <div id="save" class="panel">
            <?php echo $this->formSubmit('submit_save_changes', __('Save Changes'), array('class'=>'submit big green button')); ?>
        </div>
    </section>
</form>
<?php echo foot(); ?>
