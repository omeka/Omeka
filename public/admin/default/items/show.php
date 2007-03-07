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
	
	Event.observe(window, 'load', function() {
		$('favorite').setAttribute('href', '#');
		Event.observe("favorite", "click", setFavorite);
	});
</script>
<ul id="secondary-nav" class="navigation">
	<?php nav(array('Show Item' => uri('items/show/'.$item->id), 'Edit Item' => uri('items/edit/'.$item->id), 'Back to Items' => uri('items')));?>
</ul>

<h2><?php echo $item->title; ?></h2>

<h3>Core Metadata</h3>
<div id="core-metadata">
	
	<h4>Description</h4>
	<?php echo $item->description; ?>
	
	<h4>Publisher</h4>
<?php echo $item->publisher?>
	
	<h4>Relation</h4>
<?php echo $item->relation;?>
	
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
