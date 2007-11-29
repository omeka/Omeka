<?php head(array('body_class'=>'index', 'title'=>'Dashboard')); ?>

	<div id="primary">
		<div id="welcome">

			<div id="getting-started">
				<h1>Getting Started with Omeka</h1>
				<dl>
					<dt class="archive"><a href="<?php echo uri('items'); ?>">Archive</a></dt>
					<dd class="archive">
						<ul>
							<li><a class="add" href="<?php echo uri('items/add'); ?>">Add an item to your archive</a></li>
							<li><a class="add-collection" href="<?php echo uri('collections/add'); ?>">Add a collection to group items</a></li>
						</ul>
						<p>Manage items in your archive: add, edit, and delete items. Learn about item types and group items into collections.</p>
					</dd>
					
				<?php if(has_permission('Exhibits','browse')): ?>
					<dt class="exhibits"><a href="<?php echo uri('exhibits/browse'); ?>">Exhibits</a></dt>
					<dd class="exhibits">
						<ul>
							<li><a class="browse-exhibits" href="<?php echo uri('exhibits/browse'); ?>">Browse exhibits</a></li>
							<li><a class="add-exhibit" href="<?php echo uri('exhibits/add'); ?>">Create an exhibit</a></li>
						</ul>
						<p>Create and manage exhibits that display items from the archive.</p>
					</dd>
				<?php endif; ?>
				
				<?php if(has_permission('Users','browse')): ?>
					<dt class="users"><a href="<?php echo uri('users/browse'); ?>">Users</a></dt>
					<dd class="users">
						<ul>
							<li><a class="browse-users" href="<?php echo uri('users/browse'); ?>">Browse Users</a></li>
							<li><a class="add-user" href="<?php echo uri('users/add'); ?>">Add a User</a></li>
						</ul>
						<p>Add and manage users of various levels: from researcher to super.</p>
					</dd>
				<?php endif; ?>
				
				<?php if(has_permission('super')): ?>
					<dt class="site-settings"><a href="<?php echo uri('settings'); ?>">Settings</a></dt>
					<dd class="site-settings">
						<ul>
							<li><a class="editsettings" href="<?php echo uri('settings'); ?>">Edit General Settings</a></li>
							<li><a class="managethemes" href="<?php echo uri('themes'); ?>">Manage Themes</a></li>
							<li><a class="manageplugins" href="<?php echo uri('plugins'); ?>">Manage Plugins</a></li>
						</ul>
						<p>Manage your general settings for the site, including title, description, and themes.</p>
					</dd>
			<?php endif; ?>
			<?php if(has_permission('entities','add')): ?>
					<dt class="names"><a href="<?php echo uri('entities/browse'); ?>">Names</a></dt>
					<dd class="names">
						<ul>
							<li><a class="browse-names" href="<?php echo uri('entities/browse'); ?>">Browse Names</a></li>
						</ul>
						<p>Manage all names in your site, including people and institutions.</p>
					</dd>
					</dl>
			<?php endif; ?>
			<p class="help">Need help with Omeka? Visit our <a href="http://omeka.org/codex/">codex</a> for detailed instructions for using and customizing our application.</p>
			</div>
			
		</div>
		<div id="site-info">
			<div id="site-meta">
				<h2>Site Overview</h2>
				<p><em><?php settings('site_title'); ?></em> contains <?php echo total_items(); ?> items, in <?php echo total_collections(); ?> collections, tagged with <?php echo total_tags(); ?> keywords. There are <?php echo total_users(); ?> users.</p>
			</div>
			<div id="recent-items">
				<h2>Recent Items</h2>
				<?php $items = recent_items('5'); ?>
				<?php if(count($items) == 0):?>
					<div class="error">There are no items to display</div>	
				<?php else: ?>
				<ul>
					<?php foreach( $items as $key => $item ): ?>
						<li class="<?php if($key%2==1) echo 'even'; else echo 'odd'; ?>"><a href="<?php echo uri('items/show/'.$item->id); ?>"><span class="title"><?php  echo h($item->title); ?></span> <span class="date"><?php echo date('m.d.Y', strtotime($item->added)); ?></span></a> </li>	
					<?php endforeach; ?>
				</ul>
				
				<p id="view-all-items"><a href="<?php echo uri('items/browse'); ?>">View All Items</a></p>
				<?php endif; ?>
			</div>
			
			<div id="tag-cloud">
				<h2>Recent Tags</h2>
				<?php tag_cloud(recent_tags(), uri('items/browse/')); ?>
			</div>
		</div>
		
		
	</div>
<?php foot(); ?>