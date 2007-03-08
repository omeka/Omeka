<?php head(array('title' => 'Item'))?>

<?php error($item);?>
<script type="text/javascript" charset="utf-8">
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
		$('favorite').setAttribute('href', '#');
		Event.observe("favorite", "click", setFavorite);
		
		editableElements = document.getElementsByClassName("editable");
		
		for(i=0;i<editableElements.length;i++) {
			var editable = new EditableField(editableElements[i], editableElements[i].id, "<?php echo uri('json/items/edit/'); ?>", <?php echo $item->id;?>);
		}
	});
</script>
<ul id="secondary-nav" class="navigation">
	<?php nav(array('Show Item' => uri('items/show/'.$item->id), 'Edit Item' => uri('items/edit/'.$item->id), 'Back to Items' => uri('items')));?>
</ul>

<h2><?php echo $item->title; ?></h2>

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
	<?php echo $item->rights;?>

	
	<h4>Source</h4>
<?php echo $item->source;?>
	
	<h4>Subject</h4>
<?php echo $item->subject;?>
	
	<h4>Creator</h4>
<?php echo $item->creator;?>
	
	<h4>Additional Creator</h4>
<?php echo $item->additional_creator;?>
	
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
<h2>Tags</h2>
<ul>
	<?php $tags = $item->Tags;?>
	<?php foreach($tags as $tag):?>
	<a href="#"><?php echo $tag; ?></a>
	<?php endforeach; ?>
</ul>
<form id="tags" method="post" action="">
	<input type="text" name="tags" value="Put tag string in me" />
	<input type="submit" name="submit" value="submit">
</form>
<?php foot();?>
