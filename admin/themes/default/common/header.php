<!DOCTYPE html>
<html lang="<?php echo get_html_lang(); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?php echo __('Omeka Admin'); ?>: <?php echo settings('site_title'); echo isset($title) ? ' | ' . strip_formatting($title) : ''; ?></title>

<?php
    queue_css('style', 'all');
    queue_css('layout', 'all');
    queue_css('skeleton', 'all');
    queue_css('jquery-ui', 'all');    
    queue_js('globals');
?>

<!-- Plugin Stuff -->
<?php admin_plugin_header(); ?>

<!-- Stylesheets -->
<?php display_css(); ?>
<link href='http://fonts.googleapis.com/css?family=Arvo:400,700,400italic,700italic|Cabin:400,700,400italic,700italic' rel='stylesheet' type='text/css'>

<!-- JavaScripts -->
<?php display_js(); ?>

</head>

<?php echo body_tag(array('id' => @$bodyid, 'class' => @$bodyclass)); ?>

<header>

	<div class="container">
	
		<div id="site-title" class="one-third column">
			<?php echo link_to_home_page(settings('site_title'), array('target' => '_blank')); ?>
		</div>
	
		<nav>
			
			<?php echo common('global-nav'); ?>
			
			<ul id="user-nav">
			<?php if ($user = current_user()): ?>
				<?php
					$name = html_escape($user->name);
					if (has_permission($user, 'edit')) {
						$userLink = '<a href="' . html_escape(uri('users/edit/' . $user->id)) . '">' . $name . '</a>';
					} else {
						$userLink = $name;
					}
				?>
				<li><?php echo __('Welcome, %s', $userLink); ?></li>
			    <li><a href="<?php echo html_escape(uri('users/logout'));?>" id="logout"><?php echo __('Log Out'); ?></a></li>
			<?php endif; ?>
			</ul>

		</nav>
	
	</div>
	
</header>

<section class="container container-twelve">

	<?php echo common('content-nav'); ?>
	
	<?php if (isset($title)) : ?>
	<h1 class="section-title"><?php echo $title ?></h1>
	<?php endif; ?>
    
    <section id="content" class="container">
    
    <div class="two columns">&nbsp;</div>
    
    <div class="ten columns">
