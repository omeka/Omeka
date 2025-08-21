<div id="section-nav" role="navigation" class="navigation">
<?php
    $navArray = [
        [
            'label' => __('General'),
            'uri' => record_url($user, 'edit'),
            'resource' => $user,
            'privilege' => 'edit'
        ],
        [
            'label' => __('Change Password'),
            'uri' => record_url($user, 'change-password'),
            'resource' => $user,
            'privilege' => 'change-password'
        ],
        [
            'label' => __('API Keys'),
            'uri' => record_url($user, 'api-keys'),
            'resource' => $user,
            'privilege' => 'api-keys'
        ],
    ];
    echo nav($navArray, 'admin_navigation_users', ['user' => $user]);
?>
</div>
