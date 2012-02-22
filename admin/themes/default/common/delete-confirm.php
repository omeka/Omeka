<?php
$class = get_class($record);
$pageTitle = __('Delete %s', Inflector::titleize($class));

if (!$isPartial):
head(array('title' => $pageTitle));
?>
<h1 class="section-title"><?php echo $pageTitle; ?></h1>

	<section id="content" class="container">
	
		<div class="two columns">
			&nbsp;
		</div>
		
		<div class="ten columns">

<?php endif; ?>

		<div title="<?php echo $pageTitle; ?>">
			<h2><?php echo __('Are you sure?'); ?></h2>
			<?php echo nls2p(html_escape($confirmMessage)); ?>
			<?php echo $form; ?>
		</div>
		<?php if (!$isPartial): ?>
	</div>
<?php foot(); ?>

<?php endif; ?>
