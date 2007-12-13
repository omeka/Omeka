<?php head(array('title' => 'Item # '.$item->id, 'body_class'=>'items')); ?>
<?php common('archive-nav'); ?>

<?php js('editable');?>
<script type="text/javascript" charset="utf-8">
	
	function setFavorite() {
		if(!document.getElementById('favorite')) return;
		var opt = {
			onComplete: function(t, item) {
				if(item.favorite) {
					$('favorite').update("Favorite");
				} else {
					$('favorite').update("Not Favorite");
				}
			}
		}
		new Ajax.Request("<?php echo uri('json/items/show/');?>?makeFavorite=true&id=<?php echo h($item->id);?>", opt);
		return false;
	}

	function modifyTags() {
		//Add the tags with this request
		new Ajax.Request("<?php echo uri('items/show/'.$item->id); ?>", {
			parameters: $('tags-form').serialize(),
			method: 'post',
			//Initial tagging request must be completed before displaying the new tags
			onComplete: function(t) {
				new Ajax.Request("<?php echo uri('items/ajaxTagsField/?id='.$item->id) ?>", {
					onSuccess: function(t) {
						$('tags').hide();
						$('tags').update(t.responseText);
						Effect.Appear('tags', {duration: 1.0});
					}
				});
			}
		});
		return false;
	}
	
	Event.observe(window, 'load', function() {
		if(!$('favorite')) return;
		//Make the favorites thing work w/ AJAX
		$('favorite').setAttribute('href', 'javascript:void(0)');
		Event.observe("favorite", "click", setFavorite);
		
		$('tags-form').onsubmit = function() {
			modifyTags();
			return false;
		}
		/*
<?php if ( has_permission('Items','edit') ): ?>
			editableElements = document.getElementsByClassName("editable");
		
		for(i=0;i<editableElements.length;i++) {
			var editable = new EditableField(editableElements[i], 
											editableElements[i].id, 
											"<?php echo uri('json/items/edit/'); ?>", 
											<?php echo h($item->id);?>, 
											editableElements[i].getAttribute('rel'));
		}
<?php endif; ?>
	*/
	});
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
</script>
<div id="primary">
<?php echo flash(); ?>

<ul class="item-pagination navigation">
<li id="previous-item" class="previous">
	<?php link_to_previous_item($item,'Previous'); ?>
</li>
<li id="next-item" class="next">
	<?php link_to_next_item($item,'Next'); ?>
</li>
</ul>

<h1 id="title">#<?php echo $item->id; ?> <?php echo h($item->title); ?></h1>
<p id="edit-delete"> 
<?php 
link_to_item($item, 'edit', 'Edit', array('class'=>'edit'));
//link_to_item($item, 'delete', 'Delete', array('class'=>'delete')); 
?></p>

<div id="item-images">
<?php echo display_files($item->Files); ?>
	
</div>
<div id="core-metadata" class="showitem">

<h2>Core Metadata</h2>
	
	<div id="subject" class="field">
	<h3>Subject</h3>
	<div>
	<?php display_empty($item->subject); ?>
	</div>
	</div>
	
	<div id="description" class="field">
	<h3>Description</h3>
	<div>
	<?php display_empty($item->description,"No description available."); ?>
	</div>
	</div>
	
	<div id="creator" class="field">
	<h3>Creator</h3>
	<div>
	<?php display_empty($item->creator); ?>
	</div>
	</div>
	
	<div id="additional_creator" class="field">
	<h3>Additional Creator</h3>
	<div>
	<?php display_empty($item->additional_creator); ?>
	</div>
	</div>
	
	<div id="source" class="field">
	<h3>Source</h3>
	<div>
	<?php display_empty($item->source); ?>
	</div>
	</div>

	<div id="publisher" class="field">
	<h3>Publisher</h3>
	<div>
	<?php display_empty($item->publisher); ?>
	</div>
	</div>
	
	<div id="date" class="field">
	<h3>Date</h3>
	<div>
	<?php echo h($item->date);?>
	</div>
	</div>
	
	<div id="contributor" class="field">
	<h3>Contributor</h3>
	<div>
	<?php display_empty($item->contributor)?>
	</div>
	</div>
		
	<div id="rights" class="field">
	<h3>Rights</h3>
	<div>
	<?php display_empty($item->rights); ?>
	</div>
	</div>
	
	<div id="rights_holder" class="field">
	<h3>Rights Holder</h3>
	<div>
	<?php display_empty($item->rights_holder)?>
	</div>
	</div>
	
	<div id="relation" class="field">
	<h3>Relation</h3>
	<div>
	<?php display_empty($item->relation); ?>
	</div>
	</div>
	
	<div id="spatial-coverage" class="field">
	<h3>Spatial Coverage</h3>
	<div>
	<?php display_empty($item->spatial_coverage)?>
	</div>
	</div>
	
	<div id="temporal-coverage" class="field">
	<h3>Temporal Coverage</h3>
	<div>
	<?php display_empty($item->temporal_coverage_start); ?> &mdash; 
	<?php display_empty($item->temporal_coverage_end)?>
	</div>
	</div>
	
	<div id="language" class="field">
	<h3>Language</h3>
	<div>
	<?php display_empty($item->language); ?>
	</div>
	</div>

	<div id="provenance" class="field">
	<h3>Provenance</h3>
	<div>
	<?php display_empty($item->provenance)?>
	</div>
	</div>
	
	<div id="citation" class="field">
	<h3>Bibliographic Citation</h3>
	<div>
	<p><?php echo $item->getCitation();?></p>
	</div>
	</div>

</div>

<?php /* ?>
<div id="mark-favorite" class="field">
	<h3>Favorite</h3>
	<a href="<?php echo uri('items/show/'.$item->id).'?makeFavorite=true';?>" id="favorite"><?php if($item->isFavoriteOf($user)): echo "Favorite"; else: echo "Not favorite";endif;?></a>
</div>
<?php */ ?>

<?php if ( has_collection($item) ): ?>
	<div id="collection" class="field">
	<h3>Collection</h3>
	<div>
		<p><?php echo h($item->Collection->name); ?></p>
	</div>
	</div>
<?php endif; ?>

<div id="type-metadata" class="showitem">

<h2>Type Metadata</h2>

	<div class="field">
	<h3>Type Name</h3>
		<div id="type_id" class="editableSelect"><p><?php echo h($item->Type->name); ?></p></div>
	</div>
			
	<?php foreach($item->TypeMetadata as $name => $value): ?>
		<div class="field">
			<h3><?php echo h($name); ?></h3>
			<div><?php echo h($value); ?></div>
		</div>
	<?php endforeach; ?>

<h2>Tags</h2>
	<?php if ( has_permission('Items','tag') ): ?>
		<div id="my-tags" class="field">
		<h3>My Tags</h3>
		<form id="tags-form" method="post" action="">
			<input type="text" class="textinput" name="tags" id="tags-field" value="<?php echo tag_string(current_user_tags($item)); ?>" />
			<input type="submit" name="modify_tags" value="Add/Change Your Tags" id="tags-submit">
		</form>
		</div>
	<?php endif; ?>

	<div class="field">
		<h3>All Tags</h3>
		<div id="tags">
			<ul class="tags">
				<?php foreach( $item->Tags as $key => $tag ): ?>
					<li class="tag">
						<a href="<?php echo uri('items/browse/tag/'.urlencode($tag->name));?>" rel="<?php echo h($tag->id); ?>"><?php echo h($tag->name); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
		
<?php if(has_files($item)==null):?>
	<p>There are no files for this item. <a href="<?php echo uri('items/edit/'.$item->id); ?>">Add some</a>.</p>
<?php else: ?>

<h2>View File Metadata</h2>
	<div id="file-list">
		<ul>
	<?php foreach( $item->Files as $key => $file ): ?>
		<li><?php link_to($file, 'show', h($file->original_filename), array('class'=>'show','title'=>'View File Metadata')); ?>
		</li>
		

	<?php endforeach; ?>
	</ul>
	</div>


<?php endif;?>
	<div id="additional-metadata">
		<?php fire_plugin_hook('append_to_item_show', $item); ?>
	</div>
</div>
</div>

<?php foot();?>
