<?php
echo head(array('title' => __('Api'), 'bodyclass'=>'settings edit-api'));
echo common('settings-nav');
echo flash();
?>
<form method="post">
    <h2>Configure API</h2>
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
        <h2>Create an API Key</h2>
        <div class="field">
            <div class="two columns alpha">
                <label for="search_record_types"><?php echo __('New key label'); ?></label>
            </div>
            <div class="inputs five columns omega">
                <p class="explanation"><?php echo __(
                    'To create a new API key, enter a label for the key below. This '
                  . 'key will have all your permissions. Treat this key as you would ' 
                  . 'your password.'
                ); ?></p>
                <?php echo $this->formText('api_key_label'); ?>
            </div>
        </div>
        <h2>API Keys</h2>
        <?php if (!$this->keys): ?>
        <p>You have no API keys. Use the form above to create one.</p>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Label</th>
                    <th>Key</th>
                    <th>Last used IP</th>
                    <th>Rescind</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->keys as $key): ?>
                <tr>
                    <td><?php echo $key->label; ?></td>
                    <td><?php echo $key->key; ?></td>
                    <td><?php echo $key->ip; ?></td>
                    <td><?php echo $this->formCheckbox('api_key_rescind[]', $key->id) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
        <?php fire_plugin_hook('admin_settings_api_form', array('view' => $this)); ?>
    </section>
    <section class="three columns omega">
        <div id="save" class="panel">
            <?php echo $this->formSubmit('submit_save_changes', __('Save Changes'), array('class'=>'submit big green button')); ?>
        </div>
    </section>
</form>
<?php echo foot(); ?>
