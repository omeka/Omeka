<div id="section-nav" role="navigation">
<?php
    $navArray = array(
        array(
            'label' => __('General'),
            'uri' => record_url($user, 'edit'),
            'resource' => $user,
            'privilege' => 'edit'
        ),
        array(
            'label' => __('Change Password'),
            'uri' => record_url($user, 'change-password'),
            'resource' => $user,
            'privilege' => 'change-password'
        ),
        array(
            'label' => __('API Keys'),
            'uri' => record_url($user, 'api-keys'),
            'resource' => $user,
            'privilege' => 'api-keys'
        ),
    );
    echo nav($navArray, 'admin_navigation_users', array('user' => $user));
?>
</div>
