<?php head(array('title' => 'Item # '.item('id'), 'body_class'=>'items primary-secondary')); ?>
<h1 id="title">#<?php echo item('id');?> 
<?php echo item('Title'); ?></h1>
<p id="edit-delete"> 
<?php 
echo link_to_item('Edit', array('class'=>'edit'), 'edit'); ?></p>
<ul class="item-pagination navigation">
<li id="previous-item" class="previous">
	<?php echo link_to_previous_item('Previous'); ?>
</li>
<li id="next-item" class="next">
	<?php echo link_to_next_item('Next'); ?>
</li>
</ul>
<script type="text/javascript" charset="utf-8">
    
    //Handles tagging of items via AJAX
	function modifyTags() {
		//Add the tags with this request
		$('tags-form').request({
			onComplete: function(t) {
				$('tag-cloud').hide();
				$('tag-cloud').update(t.responseText);
				Effect.Appear('tag-cloud', {duration: 1.0});
			}		    
		});		
	}
	
	Event.observe(window, 'load', function() {
		$('tags-submit').observe('click', function(e){
		    Event.stop(e);
		    modifyTags();
		});
	});
	
	//End tagging functions
	
	//Image gallery functions
	function swapImage(which,where) {
	  var source = which.getAttribute("href");
	  where.setAttribute("src",source);
	  return false;
	}

	function imageGallery() {
		if(!document.getElementById || !document.getElementsByTagName) return;
		var mainfile = $$('#main-image img')[0];
		if(!mainfile) return;
		mainfile.setAttribute('width',null);
		mainfile.setAttribute('height',null);
		$$('#files a').each(function(el){
			el.onclick = function() {
				return swapImage(this,mainfile);
			}
		});
	}

	new Event.observe(window,'load',imageGallery);
	
	//End image gallery functions
</script>
<div id="primary">
<?php echo flash(); ?>

<div id="item-images">
<?php echo display_files_for_item(); ?>	
</div>

<div id="core-metadata" class="showitem">
<?php echo show_item_metadata(); ?>
</div>

<div id="additional-metadata">
	<?php fire_plugin_hook('append_to_item_show', $item); ?>
</div>

</div>
<div id="secondary">
    
    <div class="info-panel">
    	<h2>Bibliographic Citation</h2>
    	<div>
    	<p><?php echo item_citation();?></p>
    	</div>
    </div>
    
    <?php if ( item_belongs_to_collection() ): ?>
    	<div id="collection" class="info-panel">
    	<h2>Collection</h2>
    	<div>
    		<p><?php echo item('Collection Name'); ?></p>
    	</div>
    	</div>
    <?php endif; ?>
    
    <div id="tags" class="info-panel">
		<h2>Tags</h2>
		<div id="tag-cloud">
		    <?php common('tag-list', compact('item'), 'items'); ?>
		</div>
		
		<?php if ( has_permission('Items','tag') ): ?>
        
		<h3>My Tags</h3>
		<div id="my-tags">
		
		<form id="tags-form" method="post" action="<?php echo uri('items/modify-tags/') ?>">
		    <div class="input">
		    <input type="hidden" name="id" value="<?php echo item('id'); ?>" id="item-id">
			<input type="text" class="textinput" name="tags" id="tags-field" value="<?php echo tag_string(current_user_tags_for_item()); ?>" />
			</div>
			<input type="submit" class="submit submit-medium" name="modify_tags" value="Save Tags" id="tags-submit" />
		</form>
		</div>
		
		<?php endif; ?>
    	
	</div>
	
	<div class="info-panel">
	    <h2>View File Metadata</h2>
        	<div id="file-list">
        	    <?php if(!item_has_files()):?>
                	<p>There are no files for this item. <?php echo link_to_item('Add some', array(), 'edit'); ?>.</p>
                <?php else: ?>
        		<ul>
        	<?php while(loop_files_for_item()): ?>
        	    <?php $file = get_current_file(); ?>
        		<li><?php echo link_to($file, 'show', h($file->original_filename), array('class'=>'show','title'=>'View File Metadata')); ?></li>


        	<?php endwhile; ?>

        	</ul>
        	<?php endif;?>
        	</div>
	</div>
</div>
<?php foot();?>
