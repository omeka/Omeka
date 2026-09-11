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
    queue_css_file(['core-fonts', 'iconfonts', 'skeleton', 'jquery-ui', 'style']);

    queue_js_file(['vendor/respond', 'vendor/modernizr']);
    queue_js_file('vendor/selectivizr', 'javascripts', ['conditional' => '(gte IE 6)&(lte IE 8)']);
    queue_js_file('globals');
?>

<!-- Plugin Stuff -->
<?php fire_plugin_hook('admin_head', ['view'=>$this]); ?>

<!-- Stylesheets -->
<?php echo head_css(); ?>

<!-- JavaScripts -->
<?php echo head_js(); ?>

</head>

<?php echo body_tag(['id' => @$bodyid, 'class' => @$bodyclass]); ?>

<header>
    <a href="#content" id="skipnav"><?php echo __('Skip to main content'); ?></a>
    <?php fire_plugin_hook('admin_header_top', ['view'=>$this]); ?>
    <div id="site-title" class="two columns">
        <?php echo link_to_home_page(option('site_title') . '<span class="sr-only">' . __('(external link)') . '</span>', ['target' => '_blank']); ?>
    </div>
    <?php echo search_form(['show_advanced' => true, 'form_attributes'=> ['role'=>'search']]); ?>
    <button id="mobile-navbar-toggle" class="mobile-menu" data-target="#mobile-nav" title="<?php echo __('Main menu'); ?>" aria-label="<?php echo __('Main menu'); ?>" aria-expanded="false"><span class="admin-icon" aria-role="hidden"></span></button>
</header>

<div class="container container-twelve">
    <?php echo common('content-nav', ['title' => $title]); ?>

    <div id="content" class="ten columns omega offset-by-two" role="main" aria-labelledby="content-heading">
	    <div class="subhead">
	        <?php if (isset($title)) : ?>
	            <h1 id="content-heading" class="section-title"><?php echo $title ?></h1>
	        <?php endif; ?>
	    </div>
	    <div class="content-wrapper">
