<?php
// Layout: show;
$item = $__c->items()->findById();
include( 'subnav.php' );
?>
<script type="text/javascript" charset="utf-8">
	//<![CDATA[
	
	addLoadEvent(popUps);
	
	function markPublic( item_id )
	{
		var opt = {
			parameters:'id=' + item_id,
			method:'post'
		}
		
		ajax = new Ajax.Updater('mark-public','<?php echo $_link->to( "items", "ajaxMarkPublic" ); ?>', opt);
	}
	
	function markFav( item_id )
	{
		var opt = {
			parameters:'id=' + item_id,
			method:'post'
		}

		ajax = new Ajax.Updater('mark-favorite','<?php echo $_link->to( "items", "ajaxMarkFav" ); ?>', opt);
	}
	
	function markFeatured( item_id )
	{
		var opt = {
			parameters:'id=' + item_id,
			method:'post'
		}

		ajax = new Ajax.Updater('mark-featured','<?php echo $_link->to( "items", "ajaxMarkFeatured" ); ?>', opt);
	}
	
	function removeMyTag( tag_id, item_id, user_id, element )
	{
		var opt = {
			parameters: 'tag_id=' + tag_id + '&item_id=' + item_id + '&user_id=' + user_id,
			method:'post',
			onSuccess: function()
			{
				new Effect.Fade( element.parentNode, {duration: 0.4} );
				new Effect.Highlight('tags');
			},
			onFailure: function(t)
			{
				alert('Error: ' + t.status + ' -- ' + t.statusText );
			}
		}
		new Ajax.Updater('tags','<?php echo $_link->to('items','ajaxRemoveMyTag'); ?>', opt );
	}
	
	function addTags( tag_string, item_id )
	{
		var opt = {
			parameters: 'tags=' + tag_string + '&item_id=' + item_id,
			method:'post',
			onSuccess: function(t, json)
			{
				itemTags = $('tags');
				itemTags.innerHTML = json.objTags;
				
				myTags = $('my-tags');
				myTags.innerHTML = json.myTags;
				
				new Effect.Highlight('tags');
				new Effect.Highlight('my-tags');
			},
			onFailure: function(t)
			{
				alert('Error: ' + t.status + ' -- ' + t.statusText );
			}
		}
		new Ajax.Request('<?php echo $_link->to('items','ajaxAddMyTags'); ?>', opt );
	}
    //]]>
</script>

<ul class="item-nav navigation">
	<li class="previous"><?php if ($__c->items()->getPrevItemID()): ?><a href="<?php echo $_link->to( 'items' , 'show' ).$__c->items()->getPrevItemID()->item_id; ?>">Previous Item</a><?php endif; ?></li>
	<li class="next"><?php if ($__c->items()->getNextItemID()): ?><a href="<?php echo $_link->to( 'items' , 'show' ).$__c->items()->getNextItemID()->item_id; ?>">Next Item</a><?php endif; ?></li>
</ul>
<div id="item">
	<h2><span class="item-id">#<?php echo $item->item_id; ?></span>: <?php echo $item->item_title; ?></h2>
	<div id="primary">
		<div id="item-dublin-core" class="metadata-list">
			<h3>Core Metadata</h3>
			<dl id="item-identifier"><dt>Identifier</dt> <dd><?php echo $item->item_id; ?></dd></dl>
			<dl id="item-title"><dt>Title:</dt> <dd><?php echo $item->item_subject; ?></dd></dl>
			<dl id="item-creator"><dt>Creator:</dt> <dd><?php echo $item->item_creator; ?></dd></dl>
			<dl id="item-subject"><dt>Subject:</dt> <dd><?php echo $item->item_subject; ?></dd></dl>
			<dl id="item-description"><dt>Description:</dt> <dd><?php echo $item->item_description; ?></dd></dl>
			<dl id="item_publisher"><dt>Publisher:</dt> <dd><?php echo $item->item_publisher; ?></dd></dl>
			<dl id="item-creator-other"><dt>Additional Creator Info:</dt> <dd><?php echo $item->item_additional_creator; ?></dd></dl>
			<dl id="item-date"><dt>Date:</dt> <dd><?php echo $item->item_date ?></dd></dl>
			<dl id="item-source"><dt>Source:</dt> <dd><?php echo $item->item_source; ?></dd></dl>
			<dl id="item_language"><dt>Language:</dt> <dd><?php echo $item->item_language; ?></dd></dl>
			<dl id="item_relation"><dt>Relation:</dt> <dd><?php echo $item->item_relation; ?></dd></dl>
			<dl id="item-coverage"><dt>Coverage:</dt> <dd><?php echo $item->item_coverage; ?></dd></dl>
			<dl id="item_rights"><dt>Rights:</dt> <dd><?php echo $item->item_rights; ?></dd></dl>				
		</div>
		
		<div id="type-metadata" class="metadata-list">
			<h3>Type Metadata</h3>
		<dl id="item-type"><dt>Type:</dt> <dd><?php echo $item->type_name; ?></dd></dl>
			<?php if( $item->type_metadata ): ?>
				<?php foreach( $item->type_metadata as $k => $v ): ?>
					<dl><dt><?php echo $v['metafield_name']; ?>:</dt><dd><?php echo nl2br( $v['metatext_text'] ); ?></dd></dl>
				<?php endforeach; ?>
			<?php endif;?>
		</div>

	
	</div>

	<div id="secondary">

		<div id="mark-favorite">
			<a class="mark<?php if( $item->isFav( self::$_session->getUser()->getId() ) ): ?> favorite
			<?php endif; ?>" href="javascript:void(0)" onclick="markFav('<?php echo $item->getId(); ?>');" >
			<?php if( $item->isFav( self::$_session->getUser()->getId() ) ): ?>
			Favorite
			<?php else: ?>
			Make Favorite	
			<?php endif; ?>
			</a>
		</div>
		<div id="mark-featured">
			<a class="mark<?php if( $item->isFeatured( self::$_session->getUser()->getId() ) ): ?>
			 featured<?php endif; ?>" href="javascript:void(0)" onclick="markFeatured('<?php echo $item->getId(); ?>');" >
			<?php if( $item->isFeatured( self::$_session->getUser()->getId() ) ): ?>
Featured			<?php else: ?>
Make Featured			<?php endif; ?>
			</a>
		</div>
		<div id="mark-public">
			<a class="mark<?php if( $item->item_public ): ?>
			 public<?php endif; ?>" href="javascript:void(0)" onclick="markPublic('<?php echo $item->getId(); ?>');" >
			<?php if( $item->item_public ): ?>
Public			<?php else: ?>
Make Public			<?php endif; ?>
</a>
		</div>
		
		<div id="item-contributor-info">
			<?php if( $item->contributor->total() > 0 ): ?>
			<h3>Contributor: <a href="<?php echo $_link->to( 'contributors', 'show' ) . $item->contributor->contributor_id; ?>/"><?php echo $item->contributor->getName(); ?></a></h3>
			<p><strong>Gender:</strong> <span id="contributor_gender"><?php echo $item->contributor->contributor_gender; ?></span></p>
			<p><strong>Race:</strong> <span id="contributor_race"><?php echo $item->contributor->contributor_race; ?></span></p>
			<p><strong>Occupation:</strong> <span id="contributor_occupation"><?php echo $item->contributor->contributor_occupation; ?></span></p>
		</div>
		
		<div id="item-other-meta">
		<?php endif; ?>
		
		<?php if( $item->collection_id ): ?>
		<h2>Collection: <a href="<?php echo $_link->to( 'items', 'all' ) . '?collection='.$item->collection_id; ?>"><?php echo $__c->collections()->findById( $item->collection_id )->collection_name;?></a></h2>
		<?php endif; ?>

		<?php if( $item->files->total() > 0 ): ?>
			<h3>Files:</h3>
			<?php foreach( $item->files as $file ): ?>
				<?php if( $file->file_thumbnail_name ): ?>
				<a href="<?php echo $_link->to('files', 'show') . $file->getId(); ?>"><?php $_html->thumbnail( $file->file_thumbnail_name,
									array(	'class' => 'thumbnail',
									 		'alt'	=> $file->file_description,
									 		'title'	=> $file->file_title,
									 		'border'=> 1 ),
									100 ); ?></a>
				<?php else: ?>
					<h4><a href="<?php echo $_link->to('files', 'show') . $file->getId(); ?>"><?php echo $file->file_original_filename; ?></a></h4>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>
			<div id="item-history" class="metadata-list">
				<h3>History</h3>
					<?php if( self::$_session->isAdmin() ): ?>
						<dl><dt>Added to Archive:</dt> <dd><?php echo $item->item_added; ?></dd></dl>
						<dl><dt>Last Modified On:</dt> <dd><?php echo $item->item_modified; ?></dd></dl>
						<dl><dt>Status ( Public | Not Public ):</dt> <dd><?php echo ($item->item_public) ? 'Public' : 'Not Public';?></dd></dl>

					<?php endif; ?>

			</div>
	<div id="citation-info"><h3>Citation Information:</h3>
	<div class="citation"><?php echo $item->getCitation(); ?></div>
	</div>
			<div id="item-tags">
				<h2>Everyone&#8217;s Tags:</h2>
				<ul id="all-tags" class="tags">
				<?php foreach( $item->tags as $tag ): ?>
					<li><a rel="tag" href="<?php echo $_link->to( 'items', 'all' ); ?>?tags=<?php echo urlencode( $tag['tag_name'] ); ?>"><?php echo htmlentities( $tag['tag_name'] ); ?></a><?php if( $item->tags->nextIsValid() ) echo ','; ?></li>
				<?php endforeach; ?>
				</ul>
				<h3>My Tags:</h3>
				<ul id="my-tags" class="tags">
				<?php foreach( $item->myTags( self::$_session->getUser()->getId() ) as $mytag ): ?>
					<li><a rel="tag" href="<?php echo $_link->to( 'account', 'all' ); ?>?tags=<?php echo urlencode( $mytag['tag_name'] ); ?>"><?php echo htmlentities( $mytag['tag_name'] ); ?></a>  <span class="remove-tag"><a href="javascript:void(0);" onclick="if( confirm( 'Are you sure you want to remove this tag?' ) ){ removeMyTag('<?php echo $mytag['tag_id']; ?>', '<?php echo $item->getId(); ?>', '<?php echo self::$_session->getUser()->getId(); ?>', this ); }">x</a></span></li>
				<?php endforeach; ?>
				</ul>
			</div>

			<div id="add-tags">
				<input type="text" size="20" id="new-tags" />
				<input type="button" value="Add tags" onclick="addTags(document.getElementById('new-tags').value, '<?php echo $item->getId(); ?>');" ></input>
			</div>
			<?php if( $item->location->total() > 0): ?>
			<div id="item-location">
				<h2>Location:</h2>
				<div id="item-map"></div>
			</div>
			<?php endif; ?>
		</div>	
	</div>
</div>
<ul class="item-nav navigation">
	<li class="previous"><?php if ($__c->items()->getPrevItemID()): ?><a href="<?php echo $_link->to( 'items' , 'show' ).$__c->items()->getPrevItemID()->item_id; ?>">Previous Item</a><?php endif; ?></li>
	<li class="next"><?php if ($__c->items()->getNextItemID()): ?><a href="<?php echo $_link->to( 'items' , 'show' ).$__c->items()->getNextItemID()->item_id; ?>">Next Item</a><?php endif; ?></li>
</ul>