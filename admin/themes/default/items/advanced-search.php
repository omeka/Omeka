<?php 
if (!$isPartial): // If we are using the partial view of this search form.
head(array('title'=>'Advanced Search')); ?>
<div id="primary">    
<?php endif; ?>

<h1>Search Items</h1>

<script type="text/javascript" charset="utf-8">
//<![CDATA[

//The functions that are used by the search form can be found in search.js	
	//Here is javascript that will duplicate the advanced-search form entries
	Event.observe(window,'load', Omeka.Search.activateSearchButtons );

//]]>	
</script>
	
<form <?php echo _tag_attributes($formAttributes); ?> action="<?php echo url_for('items/browse'); ?>" method="get">
	
		<input type="text" class="textinput" name="search" value="<?php echo h($_REQUEST['search']); ?>"/>
		
		<h3>Search by Specific fields</h3>
		
		<div id="advanced-search" class="field">
			
				<?php 
				//If the form has been submitted, retain the number of search fields used and rebuild the form
				if(!empty($_GET['advanced'])) {
					$search = $_GET['advanced'];
				}else {
					$search = array(array('field'=>'','type'=>'','value'=>''));
				}
				
				//Here is where we actually build the search form
				foreach ($search as $i => $rows): ?>
					<div class="search-entry">		
					<?php 
					//The POST looks like => 
					// advanced[0] =>
						//[field] = 'description'
						//[type] = 'contains'
						//[terms] = 'foobar'
					//etc
					echo select_element(
						array('name'=>"advanced[$i][element_id]"), 
						@$rows['element_id']); ?>
					
					<?php 
						echo select(
							array('name'=>"advanced[$i][type]"),
							array('contains'=>'contains', 'does not contain'=>'does not contain', 'is empty'=>'is empty', 'is not empty'=>'is not empty'),
							@$rows['type']
						); 
					?>
					
					<?php 
						echo text(
							array('name'=>"advanced[$i][terms]", 'size'=>20),
							@$rows['terms']); 
					?>
					
					<button type="button" class="add_search">+</button>
					<button type="button" class="remove_search">-</button>
					</div>		 				
				<?php endforeach; ?>	
				
			
		</div>
		
		<div id="search-by-range" class="field">
		    <h3>Search by a range of ID#s (example: 1-4, 156, 79)</h3>
			<?php echo text(
				array('name'=>'range', 'class'=>'textinput'), 
				@$_GET['range'], 
				'Range'); ?>
		</div>
		
		<div id="search-selects">
	<?php 
	    echo label('collection-search', 'Search By Collection');
		echo select_collection(array('name'=>'collection', 'id'=>'collection-search'), $_REQUEST['collection']);
		echo label('item-type-search', 'Search By Type');
		echo select_item_type(array('name'=>'type', 'id'=>'item-type-search'), $_REQUEST['type']); 
	?>
	<?php if(has_permission('Users', 'browse')): ?>
	<?php 			
	    echo label('user-search', 'Search By User');
		echo select_user(array('name'=>'user', 'id'=>'user-search'), $_REQUEST['user']);
	?>
	<?php endif; ?>
	<?php 
	echo label('tags', 'Search By Tags'); 
	echo text(array('name'=>'tags','id'=>'tag-search','class'=>'textinput'),$_REQUEST['tags']);
	?>
	</div>
	<div id="search-checkboxes">
	<?php 
		
	    if (has_permission('Items','showNotPublic')) { echo checkbox(array('name'=>'public', 'id'=>'public'), $_REQUEST['public'], null, 'Only Public Items'); 			
	}
		echo checkbox(array('name'=>'featured', 'id'=>'featured'), $_REQUEST['featured'], null, 'Only Featured Items');
	?>
	</div>
	</fieldset>
	
	<?php fire_plugin_hook('append_to_search_form'); ?>
	<fieldset>
	    <input type="submit" name="submit_search" id="submit_search" value="Search" />
	</fieldset>
</form>

<?php if (!$isPartial): ?>
    </div> <!-- Close 'primary' div. -->
    <?php foot(); ?>
<?php endif; ?>