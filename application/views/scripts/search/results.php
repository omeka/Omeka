<?php head(array('title'=>'Browse Search Results')); ?>
	<div id="primary" class="browse">
	    <?php echo flash(); ?>
		<h1>Search Results for <?php echo html_escape('"' . $searchQuery . '"'); ?> (<?php echo html_escape($totalResults); ?>)</h1>
		<h2></h2>
		<div class="pagination top"><?php echo pagination_links(); ?></div>
		<?php
		foreach($hits as $hit) {
		    $record = get_record_for_search_hit($hit);
		    $className = get_class($record);
		    switch($className) {
		        case 'Item':
		        set_current_item($record);
?>

        	<div class="item hentry">
        		<div class="item-meta">
		    
        		<h3><?php echo link_to_item(item('Dublin Core', 'Title'), array('class'=>'permalink')); ?></h3>

        		<?php if (item_has_thumbnail()): ?>
        			<div class="item-img">
        			<?php echo link_to_item(item_square_thumbnail()); ?>						
        			</div>
        		<?php endif; ?>

        		<?php if ($text = item('Item Type Metadata', 'Text', array('snippet'=>250))): ?>
        			<div class="item-description">
        			<p><?php echo $text; ?></p>
        			</div>
        		<?php elseif ($description = item('Dublin Core', 'Description', array('snippet'=>250))): ?>
        			<div class="item-description">
        			<?php echo $description; ?>
        			</div>
        		<?php endif; ?>

        		<?php if (item_has_tags()): ?>
        			<div class="tags"><p><strong>Tags:</strong>
        			<?php echo item_tags_as_string(); ?></p>
        			</div>
        		<?php endif; ?>
		
                <?php echo plugin_append_to_items_browse_each(); ?>
        
        		</div><!-- end class="item-meta" -->
        	</div><!-- end class="item hentry" -->

<?php
		        break;
		        
		        case 'Collection':
		        set_current_collection($record);
		        
?>
	<div class="collection">
    	
    	<h2><?php echo link_to_collection(); ?></h2>

    	<div class="element">
    	<h3>Description</h3>
    	<div class="element-text"><?php echo nls2p(collection('Description', array('snippet'=>150))); ?></div>
        </div>
        
    	<div class="element">
    	<h3>Collector(s)</h3> 
    	<?php if(collection_has_collectors()): ?>
    	    <div class="element-text">
            <p><?php echo collection('Collectors', array('delimiter'=>', ')); ?></p>
    	    </div>
    	<?php endif; ?>
    	</div>

    	<p class="view-items-link"><?php echo link_to_browse_items('View the items in' . collection('Name'), array('collection' => collection('id'))); ?></p>
    	
    <?php echo plugin_append_to_collections_browse_each(); ?>
    
    </div><!-- end class="collection" -->
<?php
		        break;
		        
		        default:
		            fire_plugin_hook('search_result', $record);
		        break;
		    } 
		}
		?>
	</div><!-- end primary -->	
<?php foot(); ?>