		<div id="searchwrap" class="cbb">
		<h2>Search</h2>
			<form id="searchform" action="search.php" class="clear">
				<input type="text" name="search" />
				<input type="submit" name="submit" id="search" value="Go" />
			</form>
		</div><!--end searchwrap-->
			
		<div id="news" class="cbb">
		<h2>Recently Added</h2>
			<?php $recent = recent_items(6); ?>
			<ul>
				<?php foreach( $recent as $item ): ?>
				<li><a href="<?php echo uri('items/show/'.$item->id); ?>"><?php echo ($item->title); ?></a></li>
				<?php endforeach; ?>
			</ul>

		</div><!--end news-->
		
		<div id="tagcloud" class="cbb">
				<h2>Tags</h2>
				<?php tag_cloud(recent_tags(), uri('items')); ?>
		</div><!-- end tagcloud -->

		<div id="foot" class="cbb">
			<div id="footer">
				<a href="http://omeka.org" id="omeka-logo">Powered by Omeka</a><br />
			</div>
		</div><!-- end foot -->
		
	</div><!--end secondary-->
