<?php
    $navArray = [
        [
            'label' => __('Edit "%s"', $user->username),
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
    echo nav($navArray, 'admin_navigation_users')->setUlClass('user-nav');
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        Omeka.Users.tempUserSubmenu();
    });
</script>
