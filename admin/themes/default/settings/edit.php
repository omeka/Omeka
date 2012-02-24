<?php
$pageTitle = __('Edit General Settings');
head(array('title'=>$pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'settings primary')); ?>
<?php echo js('settings'); ?>
<script type="text/javascript">
//<![CDATA[
    jQuery(document).ready(function () {
        Omeka.Settings.checkImageMagick(
            <?php echo js_escape(uri(array("controller"=>"settings","action"=>"check-imagemagick"))); ?>,
            <?php echo js_escape(__('Test')); ?>
        );
    });
//]]>    
</script>

<h1 class="section-title"><?php echo $pageTitle; ?></h1>

	<section id="content" class="container">
	
		<div class="two columns">
			&nbsp;
		</div>
		
		<div class="ten columns">

			<?php common('settings-nav'); ?>
			<?php echo flash(); ?>
			<?php echo $this->form; ?>
	
		</div>
		
<?php foot();
