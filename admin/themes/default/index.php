<?php head(); ?>

	<div id="primary">
		<div id="welcome">

			<div id="getting-started">
				<h1>Getting Started with Omeka</h1>
				<dl class="archive">
					<dt><a href="<?php echo uri('items'); ?>">Archive</a></dt>
					<dd>
						<ul>
							<li><a class="add" href="<?php echo uri('items/add'); ?>">Add an item to your archive</a></li>
							<li><a class="add-collection" href="<?php echo uri('collections/add'); ?>">Add a collection to group items</a></li>
						</ul>
						<p>Aliquam aliquet, est a ullamcorper condimentum, tellus nulla fringilla elit, a iaculis nulla turpis sed wisi. Fusce volutpat.</p>
						
					</dd>
				</dl>
				<?php if(has_permission('Exhibits','browse')): ?>
				<dl class="exhibits">
					<dt><a href="<?php echo uri('exhibits/browse'); ?>">Exhibits</a></dt>
					<dd>
						<ul>
							<li><a class="browse-exhibits" href="<?php echo uri('exhibits/browse'); ?>">Browse exhibits</a></li>
							<li><a class="add-exhibit" href="<?php echo uri('exhibits/add'); ?>">Create an exhibit</a></li>
						</ul>
						<p>Fusce volutpat. Etiam sodales ante id nunc. Proin ornare dignissim lacus.</p>
						
					</dd>
				</dl>
				<?php endif; ?>
				
				<?php if(has_permission('Users','browse')): ?>
				<dl class="users">
					<dt><a href="<?php echo uri('users/browse'); ?>">Users</a></dt>
					<dd>
						<ul>
							<li><a class="browse-users" href="<?php echo uri('users/browse'); ?>">Browse Users</a></li>
							<li><a class="add-user" href="<?php echo uri('users/add'); ?>">Add a User</a></li>
						</ul>
						<p>Fusce volutpat. Etiam sodales ante id nunc. Proin ornare dignissim lacus.</p>
						
					</dd>
				</dl>
				<?php endif; ?>
				
				<?php if(has_permission('super')): ?>
				
				<dl class="site-settings">
					<dt><a href="<?php echo uri('settings'); ?>">Settings</a></dt>
					<dd>
						<ul>
							<li><a class="editsettings" href="<?php echo uri('settings'); ?>">Edit General Settings</a></li>
							<li><a class="managethemes" href="<?php echo uri('themes'); ?>">Manage Themes</a></li>
							<li><a class="manageplugins" href="<?php echo uri('plugins'); ?>">Manage Plugins</a></li>
						</ul>
						<p>Fusce volutpat. Etiam sodales ante id nunc. Proin ornare dignissim lacus.</p>
						
					</dd>
				</dl>
			<?php endif; ?>
			<?php //if(has_permission('names','browse')): ?>
			
				<dl class="names">
					<dt><a href="<?php echo uri('entities/browse'); ?>">Names</a></dt>
					<dd>
						<ul>
							<li><a href="<?php echo uri('entities/browse'); ?>">Browse Names</a></li>
							<li><a href="<?php echo uri('themes'); ?>">Manage Themes</a></li>
							<li><a href="<?php echo uri('plugins'); ?>">Manage Plugins</a></li>
						</ul>
						<p>Fusce volutpat. Etiam sodales ante id nunc. Proin ornare dignissim lacus.</p>
						
					</dd>
				</dl>
			<?php //endif; ?>
				<p>Need help with Omeka? Visit our <a href="http://omeka.org/codex/">codex</a> for detailed instructions for using and customizing our application.</p>
			</div>
		
	
		</div>
		<div id="site-info">
			<div id="site-meta">
				<h2>Site Totals</h2>
				<p><em><?php settings('site_title'); ?></em> contains <?php total_items(); ?> items, in <?php total_collections(); ?> collections, tagged with <?php total_tags(); ?> keywords. There are <?php total_users(); ?> users.</p>
			</div>
			<div id="recent-items">
				<h2>Recently Added Items</h2>
				<?php $items = recent_items('5'); ?>
				<ul>
					<?php foreach( $items as $key => $item ): ?>
						<li class="<?php if($key%2==1) echo 'even'; else echo 'odd'; ?>"><a href="<?php echo uri('items/show/'.$item->id); ?>"><span class="title"><?php  echo h($item->title); ?></span> <span class="date"><?php echo date('m.d.Y', strtotime($item->added)); ?></span></a> </li>	
					<?php endforeach; ?>
				</ul>
				<p id="view-all-items"><a href="<?php echo uri('items/browse'); ?>">View All Items</a></p>	
			</div>
		</div>
		
	</div>
<?php foot(); ?>