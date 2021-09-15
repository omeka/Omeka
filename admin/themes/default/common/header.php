<!DOCTYPE html>
<html lang="<?php echo get_html_lang(); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    if (isset($title)) {
        $titleParts[] = strip_formatting($title);
    }
    $titleParts[] = option('site_title');
    $titleParts[] = __('Omeka Admin');
    ?>
    <title><?php echo implode(' &middot; ', $titleParts); ?></title>

<?php
    queue_css_file(array('iconfonts', 'skeleton', 'jquery-ui', 'style'));
    queue_css_url('//fonts.googleapis.com/css2?family=Lato:ital,wght@0,400;0,700;1,400');

    queue_js_file(array('vendor/respond', 'vendor/modernizr'));
    queue_js_file('vendor/selectivizr', 'javascripts', array('conditional' => '(gte IE 6)&(lte IE 8)'));
    queue_js_file('globals');
?>

<!-- Plugin Stuff -->
<?php fire_plugin_hook('admin_head', array('view'=>$this)); ?>

<!-- Stylesheets -->
<?php echo head_css(); ?>

<!-- JavaScripts -->
<?php echo head_js(); ?>

</head>

<?php echo body_tag(array('id' => @$bodyid, 'class' => @$bodyclass)); ?>
<a href="#content" id="skipnav"><?php echo __('Skip to main content'); ?></a>

<header role="banner">
    <?php fire_plugin_hook('admin_header_top', array('view'=>$this)); ?>
    <div id="site-title" class="two columns">
        <?php echo link_to_home_page(option('site_title'), array('target' => '_blank')); ?>
    </div>

	<div id="mobile-navbar-toggle" class="mobile-menu" data-target="#navbar">...</div>
    <nav id="navbar">
        <?php echo common('global-nav'); ?>
        
        <ul id="user-nav">
        <?php if ($user = current_user()): ?>
            <?php
                $name = html_escape($user->name);
                if (is_allowed($user, 'edit')) {
                    $userLink = '<a href="' . html_escape(url('users/edit/' . $user->id)) . '">' . $name . '</a>';
                } else {
                    $userLink = $name;
                }
            ?>
            <li><?php echo __('Welcome, %s', $userLink); ?></li>
            <li><a href="<?php echo html_escape(url('users/logout'));?>" id="logout"><?php echo __('Log Out'); ?></a></li>
        <?php endif; ?>
        </ul>
    </nav>
    <?php fire_plugin_hook('admin_header_bottom', array('view'=>$this)); ?>
</header>

<div class="container container-twelve">
    <?php echo common('content-nav', array('title' => $title)); ?>

    <div id="content" class="ten columns omega offset-by-two" role="main" aria-labelledby="content-heading">
	    <div class="subhead">
	        <?php echo search_form(array('show_advanced' => true, 'form_attributes'=> array('role'=>'search'))); ?>
	        <?php if (isset($title)) : ?>
	            <h1 id="content-heading" class="section-title"><?php echo $title ?></h1>
	        <?php endif; ?>
	    </div>
	    <div class="content-wrapper">
