<?php head(array('title' => 'Item'))?>

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
	}
	
	var EditableField = Class.create();
	
	EditableField.prototype = {
		initialize: function(div, fieldName, ajaxUri, recordId) {
			this.div = div;
			this.fieldName = fieldName;
			this.ajaxUri = ajaxUri;
			this.recordId = recordId;
			this.boundMakeEditable = this.makeEditable.bindAsEventListener(this);
			Event.observe(this.div, "click", this.boundMakeEditable);
		},
		
		makeEditable: function() {
			Event.stopObserving(this.div, "click", this.boundMakeEditable); 
					
			this.text = this.div.innerHTML.strip();
			
			this.div.innerHTML = '';
			
			this.editForm = document.createElement("form");						
			this.textEdit = document.createElement("textarea");
			this.textEdit.setAttribute('name', this.fieldName);
			this.textEdit.innerHTML = this.text;
			this.editForm.appendChild(this.textEdit);
			this.div.appendChild(this.editForm);
			
			//Now add an 'Edit' link
			
			editLink = document.createElement("a");
			editLink.setAttribute("href", "#");
			editLink.innerHTML = 'Edit';
			this.div.appendChild(editLink);
			Event.observe(editLink, "click", this.sendEdit.bindAsEventListener(this));
		},
		
		sendEdit: function() {
			//Remember that noRedirect must be set to true for the ajax to work
			
			var that = this;
			var opt = {
				parameters: "noRedirect=true&"+Form.serialize(this.editForm),
				method: "post",
				onSuccess: function(t, item) {
					that.div.innerHTML = item[that.fieldName];
					Event.observe(that.div, "click", that.boundMakeEditable);
				}
			}
			
			new Ajax.Request(this.ajaxUri + "?id="+this.recordId, opt);
		}
	}
	
	Event.observe(window, 'load', function() {
		//Make the favorites thing work w/ AJAX
		$('favorite').setAttribute('href', '#');
		Event.observe("favorite", "click", setFavorite);
		
		//Make the tags work w/AJAX
		$('tags-submit').setStyle({display:"none"});
		link = document.createElement("a");
		link.setAttribute("href", "#");
		link.innerHTML = "Add Tags";
		$('tags-form').appendChild(link);
		Event.observe(link, "click", addTags);
		
/*		oldTags = document.getElementsByClassName("tag");
		lastTag = oldTags.last().getElementsByTagName("a");
		tagText = $A(lastTag).first().innerHTML;*/
		
		editableElements = document.getElementsByClassName("editable");
		
		for(i=0;i<editableElements.length;i++) {
			var editable = new EditableField(editableElements[i], editableElements[i].id, "<?php echo uri('json/items/edit/'); ?>", <?php echo $item->id;?>);
		}
	});
</script>
<ul id="secondary-nav" class="navigation">
	<?php nav(array('Show Item' => uri('items/show/'.$item->id), 'Edit Item' => uri('items/edit/'.$item->id), 'Back to Items' => uri('items')));?>
</ul>

<h2><div class="editable" id="title"><?php echo $item->title; ?></div></h2>

<h3>Core Metadata</h3>
<div id="core-metadata">
	
	
		<h4>Description</h4>
	<div class="editable" id="description">
		<?php echo $item->description; ?>
	</div>
	<h4>Publisher</h4>
<?php echo $item->publisher?>
	
	<h4>Relation</h4>
	<div class="editable" id="relation">
		<?php echo $item->relation;?>
	</div>
	
	<h4>Language</h4>
<?php echo $item->language;?>

	<h4>Coverage</h4>
<?php echo $item->coverage;?>
	
	<h4>Rights</h4>
<div class="editable" id="rights">
	<?php echo $item->rights;?>
</div>
	
	<h4>Source</h4>
<div class="editable" id="source">
<?php echo $item->source;?>
</div>
	
	<h4>Subject</h4>
<div class="editable" id="subject">
<?php echo $item->subject;?>
</div>

	<h4>Creator</h4>
<div class="editable" id="creator">
<?php echo $item->creator;?>
</div>
	
	<h4>Additional Creator</h4>
<div class="editable" id="additional_creator">
<?php echo $item->additional_creator;?>
</div>
	
	<h4>Date</h4>
<?php echo $item->date;?>

</div>
<h4>Metatext</h4>
<?php foreach($item->Metatext as $key => $metatext): ?>
<h5><?php echo $metatext->Metafield->name; ?>: <?php echo $metatext->text; ?></h5>

<?php endforeach; ?>

<div id="mark-favorite">
	<a href="<?php echo uri('items/show/'.$item->id).'?makeFavorite=true';?>" id="favorite"><?php if($item->isFavoriteOf($user)): echo "Favorite"; else: echo "Not favorite";endif;?></a>
</div>
<h2>My Tags</h2>
<div id="my-tags">
	<ul>
		<?php $myTags = $item->userTags($user);?>
		<?php foreach($myTags as $tag):?>
		<li class="my-tag"><a href="#"><?php echo $tag; ?></a></li>
		<?php endforeach; ?>
	</ul>
</div>
<h2>All Tags</h2>
<div id="tags">
	<ul>
		<?php foreach( $item->Tags as $key => $tag ): ?>
			<li class="tag"><a href="#"><?php echo $tag; ?></a></li>
		<?php endforeach; ?>
	</ul>
</div>
<form id="tags-form" method="post" action="">
	<input type="text" name="tags" value="Put tag string in me" />
	<input type="submit" name="submit" value="submit" id="tags-submit">
</form>
<?php foot();?>
