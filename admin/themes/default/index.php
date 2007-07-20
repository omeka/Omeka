<?php head(); ?>

	<div id="primary">
		
		<div id="site-info">
			<h1>Welcome to Omeka</h1>
			<h2>Site Info</h2>
			<p><em><?php settings('site_title'); ?></em> contains <?php total_items(); ?> items, in <?php total_collections(); ?> collections, tagged with <?php total_tags(); ?> keywords. There are <?php total_users(); ?> users.</p>
		</div>
		
		<div id="getting-started">
			<h2>Getting Started</h2>
			<ul>
				<li><a href="<?php echo uri('items/add'); ?>">Add an item to your archive</a></li>
				<li><a href="<?php echo uri('users'); ?>">Edit your user information</a></li>
				<li><a href="<?php echo uri('collections/add'); ?>">Add a collection to group items</a></li>
				<li><a href="<?php echo uri('exhibits/add'); ?>">Create an exhibit</a></li>
			</ul>
			<p>Need help with Omeka? Visit our <a href="http://omeka.org/codex/">codex</a> for detailed instructions for using and customizing our application.</p>
		</div>
		
		<div id="featured-item">
			<h2>Random Featured Item</h2>
			<?php 
				$featured = random_featured_item();
				thumbnail($featured);?>
			<h3><?php echo $featured->title; ?></h4>
			
		</div>
	</div>
	
	<div id="secondary">
		
		<div id="recent-items">
			<h2>Recently Added</h2>
			<?php $items = recent_items('5'); ?>
			<ul>
				<?php foreach( $items as $key => $item ): ?>
					<li class="<?php if($key%2==1) echo 'even'; else echo 'odd'; ?>"><span class="title"><a href="<?php echo uri('items/show/'.$item->id); ?>"><?php  echo $item->title; ?></a></span> <span class="date"><?php echo date('m.d.Y', strtotime($item->added)); ?></span></li>	
				<?php endforeach; ?>
			</ul>
			<p><a href="<?php echo uri('items/browse'); ?>">View All Items</a></p>	
		</div>
		
		<div id="tagcloud">
			<h2>Tag Cloud</h2>
			<?php tag_cloud(recent_tags(), uri('items/browse/')); ?>
		</div>
		
	</div>
<?php foot(); ?>