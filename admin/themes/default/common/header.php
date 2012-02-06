<!DOCTYPE html>
<html lang="<?php echo get_html_lang(); ?>">
<head>
    <meta charset="utf-8">
    <title><?php echo __('Omeka Admin'); ?>: <?php echo settings('site_title'); echo isset($title) ? ' | ' . strip_formatting($title) : ''; ?></title>

<?php
    queue_css('default', 'all');
    queue_css('jquery-ui', 'screen');
    queue_js('globals');
?>

<!-- Plugin Stuff -->
<?php admin_plugin_header(); ?>
<!-- Stylesheets -->
<?php display_css(); ?>

<!-- JavaScripts -->
<?php display_js(); ?>

</head>
<?php echo body_tag(array('id' => @$bodyid, 'class' => @$bodyclass)); ?>
    <div class="hide"><a href="#content"><?php echo __('Skip Navigation'); ?></a></div>
    <div id="wrap">
        <div id="header">
            <div id="site-title"><?php echo link_to_admin_home_page(settings('site_title')); ?></div>
            
            <div id="site-info">
            <?php if ($user = current_user()): ?>
                <?php
                    $name = html_escape($user->first_name);
                    if (has_permission($user, 'edit')) {
                        $userLink = '<a href="' . html_escape(uri('users/edit/' . $user->id) . '">' . $name . '</a>';
                    } else {
                        $userLink = $name;
                    }
                ?>
                <p id="welcome">
                    <?php echo __('Welcome, %s', $userLink); ?> |
                    <a href="<?php echo html_escape(uri('users/logout'));?>" id="logout"><?php echo __('Log Out'); ?></a>
                </p>
            <?php endif; ?>
                <?php if (has_permission('Settings', 'edit')): ?>
                    <a href="<?php echo html_escape(uri('settings')); ?>" id="settings-link"><?php echo __('Settings'); ?></a>
                <?php endif; ?>
                <?php echo link_to_home_page(__('View Public Site'), array('id'=>'public-link')); ?>
                <?php echo plugin_append_to_admin_site_info(); ?>
            </div>
            <?php echo common('primary-nav'); ?>
        </div>
        <div id="content"<?php echo isset($content_class) ? ' class="'.$content_class.'"' : ''; ?>>
