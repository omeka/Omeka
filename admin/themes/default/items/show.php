<?php head(array('title' => 'Item'))?>

<?php js('editable');?>
<?php error($item);?>
<script type="text/javascript" charset="utf-8">
	
	function addTags() {
		var opt = {
			method: "post",
			parameters: Form.serialize($('tags-form')),
			onComplete: function(t, item) {
				if(item.Errors) alert("Error: "+item.Errors);
				oldTags = document.getElementsByClassName("my-tag");
				// If the length is the same then adding the tag didn't work
				if(oldTags.length != item.MyTags.length) {
					newMyTagLi = document.createElement("li");
					newMyTagLi.innerHTML = "<a href=\"#\">"+item.MyTags.last()+"</a>";
					newMyTagLi.setAttribute('class', 'my-tag');

					newTagLi = document.createElement("li");
					newTagLi.innerHTML = newMyTagLi.innerHTML;
					newTagLi.setAttribute('class', 'tag');
					
					// Append that business
					$('my-tags').getElementsByTagName("ul")[0].appendChild(newMyTagLi);
					$('tags').getElementsByTagName("ul")[0].appendChild(newTagLi);
					
					//@todo Focus on the new content
				}
			}
		}
		
		new Ajax.Request("<?php echo uri('json/items/show/');?>?id=<?php echo $item->id;?>", opt);
		return false;
	}
	
	function setFavorite() {
		var opt = {
			onComplete: function(t, item) {
				if(item.favorite) {
					$('favorite').innerHTML = "Favorite";
				} else {
					$('favorite').innerHTML = "Not Favorite";
				}
			}
		}
		new Ajax.Request("<?php echo uri('json/items/show/');?>?makeFavorite=true&id=<?php echo $item->id;?>", opt);
		return false;
	}

	
	Event.observe(window, 'load', function() {
		//Make the favorites thing work w/ AJAX
		$('favorite').setAttribute('href', 'javascript:void(0)');
		Event.observe("favorite", "click", setFavorite);
		
		//Make the tags work w/AJAX
		$('tags-submit').setStyle({display:"none"});
		link = document.createElement("a");
		link.setAttribute("href", "javascript:void(0)");
		link.innerHTML = "Add Tags";
		$('tags-form').appendChild(link);
		Event.observe(link, "click", addTags);
		
/*		oldTags = document.getElementsByClassName("tag");
		lastTag = oldTags.last().getElementsByTagName("a");
		tagText = $A(lastTag).first().innerHTML;*/
		
		editableElements = document.getElementsByClassName("editable");
		
		for(i=0;i<editableElements.length;i++) {
			var editable = new EditableField(editableElements[i], 
											editableElements[i].id, 
											"<?php echo uri('json/items/edit/'); ?>", 
											<?php echo $item->id;?>, 
											editableElements[i].getAttribute('rel'));
		}
		
		var langSelect = document.createElement('select');
		
				
	});
</script>
<ul id="secondary-nav" class="navigation">
	<?php nav(array('Show Item' => uri('items/show/'.$item->id), 'Edit Item' => uri('items/edit/'.$item->id), 'Back to Items' => uri('items')));?>
</ul>

<h2><div class="editable" id="title"><?php echo $item->title; ?></div></h2>

<h3>Core Metadata</h3>
<div id="core-metadata">
	
	
		<h4>Description</h4>
	<div id="description" class="editable" rel="textarea">
		<?php display_empty($item->description); ?>
	</div>
	<h4>Publisher</h4>
<div id="publisher" class="editable" rel="text"><?php display_empty($item->publisher)?></div>
	
	<h4>Relation</h4>
	<div class="editable" id="relation" rel="text">
		<?php display_empty($item->relation)?>
	</div>
	
	<h4>Language</h4>
<?php display_empty($item->language)?>

	<h4>Coverage</h4>
<?php display_empty($item->coverage)?>
	
	<h4>Rights</h4>
<div class="editable" id="rights">
	<?php display_empty($item->rights)?>
</div>
	
	<h4>Source</h4>
<div class="editable" id="source">
<?php display_empty($item->source)?>
</div>
	
	<h4>Subject</h4>
<div class="editable" id="subject">
<?php display_empty($item->subject)?>
</div>

	<h4>Creator</h4>
<div class="editable" id="creator">
<?php display_empty($item->creator)?>
</div>
	
	<h4>Additional Creator</h4>
<div class="editable" id="additional_creator">
<?php display_empty($item->additional_creator)?>
</div>
	
	<h4>Date</h4>
<?php echo $item->date;?>

</div>




<div id="mark-favorite">
	<a href="<?php echo uri('items/show/'.$item->id).'?makeFavorite=true';?>" id="favorite"><?php if($item->isFavoriteOf($user)): echo "Favorite"; else: echo "Not favorite";endif;?></a>
</div>

<?php if ( $item->Collection->exists() ): ?>
	<h2>Collection</h2>

	<div id="collection">
		<?php echo $item->Collection->name; ?>
	</div>
<?php endif; ?>


<h2>Type Metadata</h2>

<h4>Type Name</h4>
<h5><div id="type_id" class="editableSelect"><?php echo $item->Type->name; ?></div></h5>

<h4>Metatext</h4>
<?php foreach($item->Metatext as $key => $metatext): ?>
<h5><?php echo $metatext->Metafield->name; ?>: <?php echo $metatext->text; ?></h5>
<?php endforeach; ?>

<h2>My Tags</h2>
<div id="my-tags">
	<ul>
		<?php $myTags = $item->userTags($user);?>
		<?php foreach($myTags as $tag):?>
		<li class="my-tag"><a href="<?php echo uri('items/browse/tag/'.$tag->name);?>"><?php echo $tag; ?></a></li>
		<?php endforeach; ?>
	</ul>
</div>
<h2>All Tags</h2>
<div id="tags">
	<ul>
		<?php foreach( $item->Tags as $key => $tag ): ?>
			<li class="tag"><a href="<?php echo uri('items/browse/tag/'.$tag->name);?>"><?php echo $tag; ?></a></li>
		<?php endforeach; ?>
	</ul>
</div>
<form id="tags-form" method="post" action="">
	<input type="text" name="tags" value="Put tag string in me" />
	<input type="submit" name="submit" value="submit" id="tags-submit">
</form>

<h2>Files</h2>
<div id="files">
	<?php foreach( $item->Files as $key => $file ): ?>
		<?php  echo $file->archive_filename; ?>
	<?php endforeach; ?>
</div>

<?php foot();?>
