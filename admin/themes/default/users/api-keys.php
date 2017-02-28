<?php
$userTitle = $user->username;
if ($userTitle != '') {
    $userTitle = ': &quot;' . html_escape($userTitle) . '&quot; ';
} else {
    $userTitle = '';
}
$userTitle = __('Edit User #%s', $user->id) . $userTitle;
echo head(array('title' => $userTitle, 'bodyclass' => 'users'));
echo common('users-nav', array('user' => $user));
echo flash();
?>
<form method="post" action="">
<h3><?php echo __('API Keys'); ?></h3>
<div class="field">
    <div class="two columns alpha">
        <label for="search_record_types"><?php echo __('New key label'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?php echo __(
            'To create a new API key, enter a label for the key below. For example, ' 
          . 'describe for what purpose it will be used. This key will have all your ' 
          . 'permissions; treat it as you would your password.'
        ); ?></p>
        <?php echo $this->formText('api_key_label'); ?>
    </div>
</div>
<?php if (!$this->keys): ?>
<div class="field">
    <?php if ($this->user->id == $this->currentUser->id): ?>
    <p><?php echo __('You have no API keys. Use the form above to create one.'); ?></p>
    <?php else: ?>
    <p><?php echo __('%s has no API keys. Use the form above to create one.', $this->user->name); ?></p>
    <?php endif; ?>
</div>
<?php else: ?>
<table>
    <thead>
        <tr>
            <th><?php echo __('Label'); ?></th>
            <th><?php echo __('Key'); ?></th>
            <th><?php echo __('Last IP'); ?></th>
            <th><?php echo __('Last accessed'); ?></th>
            <th><?php echo __('Rescind'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($this->keys as $key): ?>
        <tr>
            <td><?php echo html_escape($key->label); ?></td>
            <td style="font-family: monospace;"><?php echo $key->key; ?></td>
            <td><?php echo $key->ip ? inet_ntop($key->ip) : ''; ?></td>
            <td><?php echo $key->accessed; ?></td>
            <td><?php echo $this->formCheckbox('api_key_rescind[]', $key->id) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
<?php echo $this->formSubmit('update_api_keys', __('Update API Keys')); ?>
</form>

<?php echo foot();?>
