<!DOCTYPE html>
<html lang="<?php echo get_html_lang(); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?php echo __('Omeka Admin'); ?>: <?php echo option('site_title'); echo isset($title) ? ' | ' . strip_formatting($title) : ''; ?></title>

<?php
    queue_css_file(array('style', 'layout', 'skeleton', 'jquery-ui'));
    queue_js_file(array('globals','jquery.jeditable.mini'));
?>

<!-- Plugin Stuff -->
<?php fire_plugin_hook('admin_theme_header'); ?>

<!-- Stylesheets -->
<?php echo head_css(); ?>
<link href='http://fonts.googleapis.com/css?family=Arvo:400,700,400italic,700italic|Cabin:400,700,400italic,700italic' rel='stylesheet' type='text/css'>

<!-- JavaScripts -->
<?php echo head_js(); ?>

</head>

<?php echo body_tag(array('id' => @$bodyid, 'class' => @$bodyclass)); ?>

<header>

    <div class="container">
    
        <div id="site-title" class="two columns">
            <?php echo link_to_home_page(option('site_title'), array('target' => '_blank')); ?>
        </div>
    
        <nav>
            
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
    
    </div>
    
</header>

<section class="container container-twelve">

    <?php echo common('content-nav', array('title' => $title)); ?>

    <div class="subhead">
    
        <form id="search" action="<?php echo url('search') ?>" method="get">
            <fieldset>
                <input type="text" name="query" class="textinput" />
                <input type="submit" value="<?php echo __('Search'); ?>" class="blue" />
            </fieldset>
            <a href="#" id="advanced-site-search" class="blue button">advanced search</a>
            <div id="advanced-form">
                <p><?php echo __('Search using this query type:'); ?></p>
                <?php echo $this->formRadio('query_type', 'full_text', null, array('full_text' => __('Full text'), 'boolean' => __('Boolean'), 'exact_match' => __('Exact match'))); ?>
                <?php if ($searchRecordTypes = get_custom_search_record_types()): ?>
                    <p><?php echo __('Search only these record types:'); ?></p>
                        <?php foreach ($searchRecordTypes as $key => $value): ?>
                        <input type="checkbox" name="record_types[]" value="<?php echo $key; ?>" checked="checked" /> <?php echo $value; ?><br />
                        <?php endforeach; ?><br />
                        <?php echo $this->formSubmit(null, __('Search'), array('class' => 'blue button')); ?>
                <?php else: ?>
                    <p><a href="<?php echo url('search/settings'); ?>"><?php echo __('Go to search settings to select record types to use.'); ?></a></p>
                <?php endif; ?>
            </div>
        </form>
            
        <?php if (isset($title)) : ?>
            <?php 
                if(strlen($title) > 80) {
                    $title = substr($title,0,79) . '..."';
                } 
            ?>
            <h1 class="section-title"><?php echo $title ?></h1>
        <?php endif; ?>
    
    </div>
    
    <section class="container">
    
        <div id="content" class="ten columns offset-by-two omega">