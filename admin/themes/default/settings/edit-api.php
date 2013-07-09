<?php
echo head(array('title' => __('Settings'), 'bodyclass'=>'settings edit-api'));
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
                    'Maximum number of API results displayed per page.'
                ); ?></p>
                <?php echo $this->formText('api_per_page', get_option('api_per_page')); ?>
            </div>
        </div>
        <p><?php echo __(
        'After enabling the API, applications may be able to show, browse, add, edit, ' . 
        'and delete your site resources. Viewing protected content, as well as adding, ' . 
        'editing, and deleting content, is protected and requires key authentication. ' .
        'Each key corresponds to, and has all permissions of, an existing Omeka user. ' .
        'You can create keys on each user\'s %sedit page%s.', 
        '<a href="' . url('users') . '">', '</a>'
        ); ?></p>
        <table>
            <thead>
            <tr>
                <th><?php echo 'Resource'; ?></th>
                <th><?php echo 'show'; ?></th>
                <th><?php echo 'browse'; ?></th>
                <th><?php echo 'add'; ?></th>
                <th><?php echo 'edit'; ?></th>
                <th><?php echo 'delete'; ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($this->api_resources as $resourceName => $resourceInfo): ?>
            <?php if (in_array($resourceName, array('site', 'resources'))): continue; endif; ?>
            <tr>
                <td><?php echo $resourceName; ?></td>
                <td style="text-align: center;"><?php if (in_array('get', $resourceInfo['actions'])): echo '&#9679;'; endif; ?></td>
                <td style="text-align: center;"><?php if (in_array('index', $resourceInfo['actions'])): echo '&#9679;'; endif; ?></td>
                <td style="text-align: center;"><?php if (in_array('post', $resourceInfo['actions'])): echo '&#9679;'; endif; ?></td>
                <td style="text-align: center;"><?php if (in_array('put', $resourceInfo['actions'])): echo '&#9679;'; endif; ?></td>
                <td style="text-align: center;"><?php if (in_array('delete', $resourceInfo['actions'])): echo '&#9679;'; endif; ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php fire_plugin_hook('admin_settings_api_form', array('view' => $this)); ?>
    </section>
    <section class="three columns omega">
        <div id="save" class="panel">
            <?php echo $this->formSubmit('submit_save_changes', __('Save Changes'), array('class'=>'submit big green button')); ?>
        </div>
    </section>
</form>
<?php echo foot(); ?>
