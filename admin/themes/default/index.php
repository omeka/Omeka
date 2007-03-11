<?php head(); ?>	
	<div id="primary">
		<div id="user-info">
			<h2>Welcome, Jeremy!</h2>
			<ul>
				<li>User Info 1</li>
				<li>User Info 1</li>
				<li>User Info 1</li>
				<li>User Info 1</li>
			</ul>
			<p><a href="#">Change your info</a></p>
		</div>
		
		<div id="site-info">
			<h2>Site Info</h2>
			<p><em>Web Design History</em> contains <?php total_items(); ?> items, in <?php total_collections(); ?> collections, tagged with <?php total_tags(); ?> keywords. There are <?php total_users(); ?> users.</p>
		</div>
		
		<div id="omeka-news">
			<h2>Omeka News</h2>
			<ul>
				<li><span class="title"><a href="#">Sed Vel Purus</a></span> <span class="date">January 21, 2007</span></li>
				<li><span class="title"><a href="#">Cras Ultrices</a></span> <span class="date">January 24, 2007</span></li>
				<li><span class="title"><a href="#">Nulla Porttitor</a></span> <span class="date">January 24, 2007</span></li>
			</ul>
		</div>
	</div>
	
	<div id="secondary">

		
		<div id="recent-items">
			<h2>Recently Added</h2>
			<?php $recent = recent_items(10);	?>
			<ul>
				<?php foreach( $recent as $key => $item ): ?>
					<li><span class="title"><a href="#"><?php  echo $item->title; ?></a></span> <span class="date"><?php echo $item->added; ?></span></li>
				<?php endforeach; ?>
				
		</div>
		
		<div id="tagcloud">
			<h2>Tag Cloud</h2>
			<?php

				tag_cloud(recent_tags(), 3, uri('items'), 4, 1);

			?>
		</div>
	</div>
<?php foot(); ?>