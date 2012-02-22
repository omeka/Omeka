<?php 
$pageTitle = __('Add a Collection');
head(array('title'=>$pageTitle, 'bodyclass'=>'collections')); ?>
<h1 class="section-title"><?php echo $pageTitle; ?></h1>

	<section id="content" class="container">
	
		<div class="two columns">
			&nbsp;
		</div>
		
		<div class="ten columns">
		
			<form method="post">
				<?php include 'form.php';?>
				<div id="save" class="three columns omega">
					<div class="panel">
						<input type="submit" class="big green button" name="submit" value="<?php echo __('Save Collection'); ?>" />
					</div>
				</div>
			</form>
		</div>
<?php foot(); ?>
