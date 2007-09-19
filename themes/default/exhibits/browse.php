<?php head(); ?>

<script type="text/javascript" charset="utf-8">
//<![CDATA[
	Event.observe(window,'load',function() {
		var deleteLinks = document.getElementsByClassName('delete-link');
		for (var i=0; i < deleteLinks.length; i++) {
			deleteLinks[i].onclick = function() {
				return confirm( 'Are you sure you want to delete this exhibit and all of its data from the archive?' );
			};
		};
	});
//]]>	
</script>
<?php common('exhibits-nav'); ?>
<div id="primary">
	<?php $exhibits = exhibits(); 
	if($exhibits):
	?>
	<h2>Exhibits</h2>
<div id="exhibits">
		
<?php foreach( $exhibits as $key=>$exhibit ): ?>
	<div class="exhibit <?php if($key%2==1) echo ' even'; else echo ' odd'; ?>">
		<h3><?php link_to_exhibit($exhibit); ?></h3>
		<div class="description"><?php echo nls2p($exhibit->description); ?></div>
		<div class="tags"><?php echo tag_string($exhibit, uri('exhibits/browse/tag/')); ?></div>
	</div>
<?php endforeach; ?>
</div>
<?php else: ?>
	<p>You have no exhibits. Please add some in the admin.</p>
	<?php endif; ?>
</div>
<?php foot(); ?>