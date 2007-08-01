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
		/*
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
	*/
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

<h1><div class="editable" id="title" rel="text"><?php echo $item->title; ?></div></h1>

<h2>Core Metadata</h2>
<div id="core-metadata">
	
	<h3>Subject</h3>
	<div class="editable" id="subject" rel="text">
	<?php display_empty($item->subject); ?>
	</div>
	
	<h3>Description</h3>
	<div id="description" class="editable" rel="textarea">
	<?php display_empty($item->description,"No description available."); ?>
	</div>
	
	<h3>Creator</h3>
	<div class="editable" id="creator" rel="text">
	<?php display_empty($item->creator); ?>
	</div>
	
	<h3>Additional Creator</h3>
	<div class="editable" id="additional_creator" rel="text">
	<?php display_empty($item->additional_creator); ?>
	</div>
	
	<h3>Source</h3>
	<div class="editable" id="source" rel="text">
	<?php display_empty($item->source); ?>
	</div>

	<h3>Publisher</h3>
	<div id="publisher" class="editable" rel="text">
	<?php display_empty($item->publisher); ?>
	</div>
	
	<h3>Date</h3>
	<div>
	<?php echo $item->date;?>
	</div>
	
	<h3>Contributor</h3>
	<div class="editable" id="contributor" rel="text">
	<?php display_empty($item->contributor)?>
	</div>
	
	<h3>Rights</h3>
	<div class="editable" id="rights" rel="text">
	<?php display_empty($item->rights); ?>
	</div>
	
	<h3>Rights Holder</h3>
	<div class="editable" id="rights_holder" rel="text">
	<?php display_empty($item->rights_holder)?>
	</div>

	<h3>Relation</h3>
	<div class="editable" id="relation" rel="text">
	<?php display_empty($item->relation); ?>
	</div>
	
	<h3>Spatial Coverage</h3>
	<div id="spatial-coverage" class="editable" rel="text">
	<?php display_empty($item->spatial_coverage)?>
	</div>
	
	<h3>Temporal Coverage</h3>
	<div id="temporal-coverage">
	<?php display_empty($item->temporal_coverage_start); ?> &mdash; 
	<?php display_empty($item->temporal_coverage_end)?>
	</div>
	
	<h3>Language</h3>
	<div class="editable" id="language" rel="text">
	<?php display_empty($item->language); ?>
	</div>

	<h3>Provenance</h3>
	<div class="editable" id="provenance" rel="text">
	<?php display_empty($item->provenance)?>
	</div>
	
	<h3>Bibliographic Citation</h3>
	<div class="editable" id="citation" rel="text">
	<?php display_empty($item->getCitation());?>
	</div>

</div>




<div id="mark-favorite">
	<a href="<?php echo uri('items/show/'.$item->id).'?makeFavorite=true';?>" id="favorite"><?php if($item->isFavoriteOf($user)): echo "Favorite"; else: echo "Not favorite";endif;?></a>
</div>

<?php if ( $item->Collection->exists() ): ?>
	<h3>Collection</h3>

	<div id="collection">
		<?php echo $item->Collection->name; ?>
	</div>
<?php endif; ?>


<h2>Type Metadata</h2>

<h3>Type Name</h3>
<div id="type_id" class="editableSelect"><?php echo $item->Type->name; ?></div>

<?php foreach($item->TypeMetadata as $name => $value): ?>
<h3><?php echo $name; ?></h3>
<div><?php echo $value; ?></div>
<?php endforeach; ?>

<h2>Tags</h2>
<?php if ( has_permission('Items','tag') ): ?>
<h3>My Tags</h3>
<div id="my-tags">
	<form id="tags-form" method="post" action="">
	<input type="text" name="tags" id="tags-field" value="<?php echo tag_string(current_user_tags($item)); ?>" />
	<input type="submit" name="modify_tags" value="Modify Your Tags" id="tags-submit">
</form>
</div>
<?php endif; ?>

<h3>All Tags</h3>
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
