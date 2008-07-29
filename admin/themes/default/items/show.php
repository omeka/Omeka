<?php head(array('title' => 'Item # '.item('id'), 'body_class'=>'items')); ?>
<?php common('archive-nav'); ?>

<script type="text/javascript" charset="utf-8">
    
    //Handles tagging of items via AJAX
	function modifyTags() {
		//Add the tags with this request
		$('tags-form').request({
			onComplete: function(t) {
				$('tags').hide();
				$('tags').update(t.responseText);
				Effect.Appear('tags', {duration: 1.0});
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

<ul class="item-pagination navigation">
<li id="previous-item" class="previous">
	<?php echo link_to_previous_item('Previous'); ?>
</li>
<li id="next-item" class="next">
	<?php echo link_to_next_item('Next'); ?>
</li>
</ul>

<h1 id="title">#<?php echo item('id');?> 
<?php echo item('Title', ', '); //Titles should all be displayed, separated by , ?></h1>
<p id="edit-delete"> 
<?php 
echo link_to_item('edit', 'Edit', array('class'=>'edit')); ?></p>

<div id="item-images">
<?php echo display_files_for_item(); ?>
	
</div>
<div id="core-metadata" class="showitem">

<h2>Core Metadata</h2>
	
	<?php $coreElementSet = array(
	    'Subject', 
	    'Description',
	    'Creator',
	    'Additional Creator',
	    'Source',
	    'Publisher',
	    'Date',
	    'Contributor',
	    'Rights',
	    'Rights Holder',
	    'Relation',
	    'Format',
	    'Spatial Coverage',
	    'Temporal Coverage',
	    'Language',
	    'Provenance',
	    'Citation'); ?>
	
	<?php foreach ($coreElementSet as $field): ?>
	   <div id="<?php echo text_to_id($field); ?>" class="field">
	       <h3><?php echo $field; ?></h3>
	       <div>
	           <?php echo display_empty(item($field, '')); ?>
	        </div>
	   </div>
	<?php endforeach ?>
	
	<div id="temporal-coverage" class="field">
	<h3>Temporal Coverage</h3>
	<div>
	<?php echo item('Temporal Coverage', ' '); 
	/** 
	 * @todo This is stored in a special format in the DB so it should be 
	 * formatted w/ a filter before display.  Was previously the two dates
	 * separated by an &mdash;
	 */ ?>
	</div>
	</div>
	
	<div id="citation" class="field">
	<h3>Bibliographic Citation</h3>
	<div>
	<p><?php echo item_citation();?></p>
	</div>
	</div>

</div>

<?php if ( item_belongs_to_collection() ): ?>
	<div id="collection" class="field">
	<h3>Collection</h3>
	<div>
		<p><?php echo item('Collection Name'); ?></p>
	</div>
	</div>
<?php endif; ?>

<div id="type-metadata" class="showitem">

<h2>Type Metadata</h2>

	<div class="field">
	<h3>Type Name</h3>
		<div id="type_id" class="editableSelect"><p><?php echo item('Item Type Name'); ?></p></div>
	</div>
			
	<?php foreach(item_type_elements() as $field => $textSet): ?>
		<div class="field">
			<h3><?php echo $field; ?></h3>
			<ul><li><?php echo join('</li><li>', $textSet); ?></li></ul>
		</div>
	<?php endforeach; ?>

<h2>Tags</h2>
	<?php if ( has_permission('Items','tag') ): ?>
		<div id="my-tags" class="field">
		<h3>My Tags</h3>
		<form id="tags-form" method="post" action="<?php echo uri('items/modify-tags/') ?>">
		    <input type="hidden" name="id" value="<?php echo item('id'); ?>" id="item-id">
			<input type="text" class="textinput" name="tags" id="tags-field" value="<?php echo tag_string(current_user_tags_for_item()); ?>" />
			<input type="submit" name="modify_tags" value="Add/Change Your Tags" id="tags-submit">
		</form>
		</div>
	<?php endif; ?>

	<div class="field">
		<h3>All Tags</h3>
		<div id="tags">
			<ul class="tags">
				<?php common('tag-list', compact('item'), 'items'); ?>
			</ul>
		</div>
	</div>
		
<?php if(!item_has_files()):?>
	<p>There are no files for this item. <?php echo link_to_item('edit', 'Add some'); ?>.</p>
<?php else: ?>

<h2>View File Metadata</h2>
	<div id="file-list">
		<ul>
	<?php while(loop_files_for_item()): ?>
	    <?php $file = get_current_file(); ?>
		<li><?php echo link_to($file, 'show', h($file->original_filename), array('class'=>'show','title'=>'View File Metadata')); ?>
		</li>
		

	<?php endwhile; ?>
	</ul>
	</div>


<?php endif;?>
	<div id="additional-metadata">
		<?php fire_plugin_hook('append_to_item_show', $item); ?>
	</div>
</div>
</div>

<?php foot();?>
