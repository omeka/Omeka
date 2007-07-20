<?php head(array('title' => 'Item')); ?>
<?php common('archive-nav'); ?>

<?php js('editable');?>
<script type="text/javascript" charset="utf-8">

	
	function setFavorite() {
		var opt = {
			onComplete: function(t, item) {
				if(item.favorite) {
					$('favorite').update("Favorite");
				} else {
					$('favorite').update("Not Favorite");
				}
			}
		}
		new Ajax.Request("<?php echo uri('json/items/show/');?>?makeFavorite=true&id=<?php echo $item->id;?>", opt);
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
		//Make the favorites thing work w/ AJAX
		$('favorite').setAttribute('href', 'javascript:void(0)');
		Event.observe("favorite", "click", setFavorite);
		
		$('tags-form').onsubmit = function() {
			modifyTags();
			return false;
		}
		
<?php if ( has_permission('Items','edit') ): ?>
			editableElements = document.getElementsByClassName("editable");
		
		for(i=0;i<editableElements.length;i++) {
			var editable = new EditableField(editableElements[i], 
											editableElements[i].id, 
											"<?php echo uri('json/items/edit/'); ?>", 
											<?php echo $item->id;?>, 
											editableElements[i].getAttribute('rel'));
		}
<?php endif; ?>
	
	});
	/*
	var checkJS = document.getElementById;

	if (checkJS) {
	        document.write('<style type="text/css">ul.items-nav{display: none;}</style>');
	}*/
</script>
<div id="primary">
<ul id="tertiary-nav" class="items-nav navigation">
	<?php 
		$tertiary_nav['Show Item'] = uri('items/show/'.$item->id);
		if(has_permission('Items','edit')) {
			$tertiary_nav['Edit Item'] = uri('items/edit/'.$item->id);
		}
		$tertiary_nav['Back to Items'] = uri('items');
	?>
	
	<?php nav($tertiary_nav);?>
</ul>
<?php echo flash(); ?>

<h2><div class="editable" id="title" rel="text"><?php echo $item->title; ?></div></h2>

<h3>Core Metadata</h3>
<div id="core-metadata">
	
	<h4>Description</h4>
	<div id="description" class="editable" rel="textarea">
	<?php display_empty($item->description,"No description available."); ?>
	</div>
	
	<h4>Publisher</h4>
	<div id="publisher" class="editable" rel="text">
	<?php display_empty($item->publisher); ?>
	</div>
	
	<h4>Relation</h4>
	<div class="editable" id="relation" rel="text">
	<?php display_empty($item->relation); ?>
	</div>
	
	<h4>Language</h4>
	<div class="editable" id="language" rel="text">
	<?php display_empty($item->language); ?>
	</div>

	<h4>Spatial Coverage</h4>
	<div id="coverage" class="editable" rel="text">
	<?php display_empty($item->spatial_coverage)?>
	</div>
	
	<h4>Temporal Coverage</h4>
	<?php display_empty($item->temporal_coverage_start); ?> &mdash; 
	<?php display_empty($item->temporal_coverage_end)?>
	
	<h4>Rights</h4>
	<div class="editable" id="rights" rel="text">
	<?php display_empty($item->rights); ?>
	</div>
	
	<h4>Rights Holder</h4>
	<div class="editable" id="rights_holder" rel="text">
	<?php display_empty($item->rights_holder)?>
	</div>
	
	<h4>Contributor</h4>
	<div class="editable" id="contributor" rel="text">
	<?php display_empty($item->contributor)?>
	</div>
	
	<h4>Provenance</h4>
	<div class="editable" id="provenance" rel="text">
	<?php display_empty($item->provenance)?>
	</div>
	
	<h4>Citation</h4>
	<div class="editable" id="citation" rel="text">
	<?php display_empty($item->getCitation());?>
	</div>
	
	<h4>Source</h4>
	<div class="editable" id="source" rel="text">
	<?php display_empty($item->source); ?>
	</div>
	
	<h4>Subject</h4>
	<div class="editable" id="subject" rel="text">
	<?php display_empty($item->subject); ?>
	</div>

	<h4>Creator</h4>
	<div class="editable" id="creator" rel="text">
	<?php display_empty($item->creator); ?>
	</div>
	
	<h4>Additional Creator</h4>
	<div class="editable" id="additional_creator" rel="text">
	<?php display_empty($item->additional_creator); ?>
	</div>
	
	<h4>Date</h4>
	<div>
	<?php echo $item->date;?>
	</div>

</div>




<div id="mark-favorite">
	<a href="<?php echo uri('items/show/'.$item->id).'?makeFavorite=true';?>" id="favorite"><?php if($item->isFavoriteOf($user)): echo "Favorite"; else: echo "Not favorite";endif;?></a>
</div>

<?php if ( $item->Collection->exists() ): ?>
	<h4>Collection</h4>

	<div id="collection">
		<?php echo $item->Collection->name; ?>
	</div>
<?php endif; ?>


<h3>Type Metadata</h3>

<h4>Type Name</h4>
<div id="type_id" class="editableSelect"><?php echo $item->Type->name; ?></div>

<?php foreach($item->Metatext as $key => $metatext): ?>
<h4><?php echo $metatext->Metafield->name; ?></h4>
<div><?php echo $metatext->text; ?></div>
<?php endforeach; ?>

<h3>Tags</h3>
<?php if ( has_permission('Items','tag') ): ?>
<h4>My Tags</h4>
<div id="my-tags">
	<form id="tags-form" method="post" action="">
	<input type="text" name="tags" id="tags-field" value="<?php echo tag_string(current_user_tags($item)); ?>" />
	<input type="submit" name="modify_tags" value="Modify Your Tags" id="tags-submit">
</form>
</div>
<?php endif; ?>

<h4>All Tags</h4>
<div id="tags">
	<ul class="tags">
		<?php foreach( $item->Tags as $key => $tag ): ?>
		<li class="tag">
			<a href="<?php echo uri('items/browse/tag/'.urlencode($tag->name));?>" rel="<?php echo $tag->id; ?>"><?php echo $tag; ?></a>
		</li>
		<?php endforeach; ?>
	</ul>
</div>


<?php if(!has_files($item)):?>
	<p>There are no files for this item. <a href="<?php echo uri('items/edit/'.$item->id); ?>">Add some</a>.</p>
<?php else: ?>

<h2>Files</h2>
<div id="files">	
	<?php foreach( $item->Files as $key => $file ): ?>

		<a href="<?php echo uri('files/show/'.$file->id); ?>">
			<?php if($file->hasThumbnail()): ?>
			<?php thumbnail($file, array('class'=>'thumb')); ?>
			<?php else: ?>
			<?php echo $file->original_filename; ?>
			<?php endif; ?>
		</a>
	<?php endforeach; ?>
</div>

<?php endif;?>
</div>
<?php foot();?>
