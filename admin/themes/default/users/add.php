<?php
$pageTitle = __('Add New User');
head(array('title'=>$pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'users primary'));?>

	<h1 class="section-title"><?php echo $pageTitle; ?></h1>

	<section id="content" class="container">
	
		<div class="two columns">
			&nbsp;
		</div>
		
		<div class="ten columns">
			
			<?php echo $this->form; ?>
		</div>
		
<?php foot();?>
