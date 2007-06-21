<?php head(); ?>

<?php common('archive-nav'); ?>
<ul id="tertiary-nav" class="navigation">
	<?php 
		if(has_permission('Items','add')) {
			nav(array('Browse Items' => uri('items/browse'), 'Add Item' => uri('items/add')));
		}
	?>
</ul>
<?php if ( total_results(true) ): ?>

	<h2>Browse Items (<?php echo total_results(true);?> items total)</h2>
	<div class="archive-meta">
		<form id="search" action="<?php echo uri('items/browse'); ?>">
			<fieldset>
				<?php 
					checkbox(array('name'=>'recent'), $_REQUEST['recent'], null, 'View Most Recent Items'); 
				?>
			</fieldset>
			
			<fieldset>
			<?php 
				select(array('name'=>'collection'), collections(), $_REQUEST['collection'], 'Filter by Collection', 'id', 'name');
				select(array('name'=>'type'), types(), $_REQUEST['type'], 'Filter by Type', 'id', 'name'); 
			?>
			<label>Filter by Tags<input type="text" name="tags" value="<?php echo $_REQUEST['tags']; ?>" /></label>
			</fieldset>
			
			<fieldset>
			<?php 
				checkbox(array('name'=>'public', 'id'=>'public'), $_REQUEST['public'], null, 'Only Public Items'); 
				checkbox(array('name'=>'featured', 'id'=>'featured'), $_REQUEST['featured'], null, 'Only Featured Items');
			?>
			</fieldset>
			
			<fieldset>
			<input type="text" name="search" value="<?php echo $_REQUEST['search']; ?>"/>
			<input type="submit" name="submit_search" value="Search" />
			</fieldset>
		</form>
		<div class="pagination"><?php echo pagination(); ?></div>
	</div>

	<table id="items" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
			<th scope="col">ID</th>
			<th scope="col">Title</th>
			<th scope="col">Type</th>
			<th scope="col">Creator</th>
			<th scope="col">Date Added</th>
			</tr>
		</thead>
		<tbody>
	<?php foreach($items as $key => $item): ?>
	<tr class="item<?php if($key%2==1) echo ' even'; else echo ' odd'; ?>">
		<td scope="row"><?php echo $item->id;?></td> 
		<td><a href="<?php echo uri('items/show/'.$item->id); ?>" class="permalink"><?php echo $item->title; ?></a></td>
		<td><?php echo $item->Type->name; ?></td>
		<td><?php echo $item->creator; ?></td>	
		<td><?php echo date('m.d.Y', strtotime($item->added)); ?></td>
	</tr>
	<?php endforeach; ?>
	</tbody>
	</table>

<?php elseif(!total_items(true)): ?>
	<h2>There are no items in the archive yet.
	
	<?php if(has_permission('Items','add')): ?>
		  Why don't you <a href="<?php echo uri('items/add'); ?>">add some</a>?</h2>
	<?php endif; ?>
	
<?php else: ?>
	<h2>The query searched <?php total_items(); ?> items and returned no results.</h2>
<?php endif; ?>


<?php foot(); ?>