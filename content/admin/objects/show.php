<?php
// Layout: show;
$object = $__c->objects()->findById();
include( 'subnav.php' );
?>
<script type="text/javascript" charset="utf-8">
	//<![CDATA[
	
	addLoadEvent(popUps);
	
	function markFav( object_id )
	{
		var opt = {
			parameters:'id=' + object_id,
			method:'post'
		}

		ajax = new Ajax.Updater('mark-favorite','<?php echo $_link->to( "objects", "ajaxMarkFav" ); ?>', opt);
	}
	
	function markFeatured( object_id )
	{
		var opt = {
			parameters:'id=' + object_id,
			method:'post'
		}

		ajax = new Ajax.Updater('mark-featured','<?php echo $_link->to( "objects", "ajaxMarkFeatured" ); ?>', opt);
	}
	
	function removeMyTag( tag_id, object_id, user_id, element )
	{
		var opt = {
			parameters: 'tag_id=' + tag_id + '&object_id=' + object_id + '&user_id=' + user_id,
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
		new Ajax.Updater('tags','<?php echo $_link->to('objects','ajaxRemoveMyTag'); ?>', opt );
	}
	
	function addTags( tag_string, object_id )
	{
		var opt = {
			parameters: 'tags=' + tag_string + '&object_id=' + object_id,
			method:'post',
			onSuccess: function(t, json)
			{
				objectTags = $('tags');
				objectTags.innerHTML = json.objTags;
				
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
		new Ajax.Request('<?php echo $_link->to('objects','ajaxAddMyTags'); ?>', opt );
	}
    //]]>
</script>

<ul class="object-nav navigation">
	<li class="previous"><?php if ($__c->objects()->getPrevObjectID()): ?><a href="<?php echo $_link->to( 'objects' , 'show' ).$__c->objects()->getPrevObjectID()->object_id; ?>">Previous Item</a><?php endif; ?></li>
	<li class="next"><?php if ($__c->objects()->getNextObjectID()): ?><a href="<?php echo $_link->to( 'objects' , 'show' ).$__c->objects()->getNextObjectID()->object_id; ?>">Next Item</a><?php endif; ?></li>
</ul>
<div id="object">
	<h2><span class="object-id">#<?php echo $object->object_id; ?></span>: <?php echo $object->object_title; ?></h2>
	<div id="primary">
		<div id="object-dublin-core" class="metadata-list">
			<h3>Core Metadata</h3>
			<dl id="object-identifier"><dt>Identifier</dt> <dd>0001234</dd></dl>
			<dl id="object-title"><dt>Title:</dt> <dd>Lorem Ipsum</dd></dl>
			<dl id="object-creator"><dt>Creator:</dt> <dd>John Doe</dd></dl>
			<dl id="object-subject"><dt>Subject:</dt> <dd>Trees</dd></dl>
			<dl id="object-description"><dt>Description:</dt> <dd>Lorem ipsum dolor sit amet.</dd></dl>
			<dl id="object_publisher"><dt>Publisher:</dt> <dd><?php echo $object->object_publisher; ?></dd></dl>
			<dl id="object-creator-other"><dt>Creator (Other):</dt> <dd>Jane Doe</dd></dl>
			<dl id="object-date"><dt>Date:</dt> <dd><?php echo $object->object_date ?></dd></dl>
			<dl id="object-source"><dt>Source:</dt> <dd>National Geographic</dd></dl>
			<dl id="object_language"><dt>Language:</dt> <dd><?php echo $object->object_language; ?></dd></dl>
			<dl id="object_relation"><dt>Relation:</dt> <dd><?php echo $object->object_relation; ?></dd></dl>
			<dl id="object-coverage"><dt>Coverage:</dt> <dd><?php echo $object->object_coverage; ?></dd></dl>
			<dl id="object_rights"><dt>Rights:</dt> <dd><?php echo $object->object_rights; ?></dd></dl>				
		</div>
		
		<div id="category-metadata" class="metadata-list">
			<h3>Type Metadata</h3>
		<dl id="object-category"><dt>Type:</dt> <dd><?php echo $object->category_name; ?></dd></dl>
			<?php if( $object->category_metadata ): ?>
				<?php foreach( $object->category_metadata as $k => $v ): ?>
					<dl><dt><?php echo $v['metafield_name']; ?>:</dt><dd><?php echo nl2br( $v['metatext_text'] ); ?></dd></dl>
				<?php endforeach; ?>
			<?php endif;?>
		</div>

	
	</div>

	<div id="secondary">

		<div id="mark-favorite">
			<a class="mark<?php if( $object->isFav( self::$_session->getUser()->getId() ) ): ?> favorite
			<?php endif; ?>" href="javascript:void(0)" onclick="markFav('<?php echo $object->getId(); ?>');" >
			<?php if( $object->isFav( self::$_session->getUser()->getId() ) ): ?>
			Favorite
			<?php else: ?>
			Make Favorite	
			<?php endif; ?>
			</a>
		</div>
		<div id="mark-featured">
			<a class="mark<?php if( $object->isFeatured( self::$_session->getUser()->getId() ) ): ?>
			 featured<?php endif; ?>" href="javascript:void(0)" onclick="markFeatured('<?php echo $object->getId(); ?>');" >
			<?php if( $object->isFeatured( self::$_session->getUser()->getId() ) ): ?>
Featured			<?php else: ?>
Make Featured			<?php endif; ?>
			</a>
		</div>
		<div id="mark-public">
			<a href="#" class="mark">Make Public</a>
		</div>
		
		<div id="object-contributor-info">
			<?php if( $object->contributor->total() > 0 ): ?>
			<h3>Contributor: <a href="<?php echo $_link->to( 'contributors', 'show' ) . $object->contributor->contributor_id; ?>/"><?php echo $object->contributor->getName(); ?></a></h3>
			<p><strong>Gender:</strong> <span id="contributor_gender"><?php echo $object->contributor->contributor_gender; ?></span></p>
			<p><strong>Race:</strong> <span id="contributor_race"><?php echo $object->contributor->contributor_race; ?></span></p>
			<p><strong>Occupation:</strong> <span id="contributor_occupation"><?php echo $object->contributor->contributor_occupation; ?></span></p>
		</div>
		
		<div id="object-other-meta">
		<?php endif; ?>
		
		<?php if( $object->collection_id ): ?>
		<h2>Collection: <a href="<?php echo $_link->to( 'objects', 'all' ) . '?collection='.$object->collection_id; ?>"><?php echo $__c->collections()->findById( $object->collection_id )->collection_name;?></a></h2>
		<?php endif; ?>

		<?php if( $object->files->total() > 0 ): ?>
			<h3>Files:</h3>
			<?php foreach( $object->files as $file ): ?>
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
			<div id="object-history" class="metadata-list">
				<h3>History</h3>
					<?php if( self::$_session->isAdmin() ): ?>
						<dl><dt>Added to Archive:</dt> <dd><?php echo $object->object_added; ?></dd></dl>
						<dl><dt>Last Modified On:</dt> <dd><?php echo $object->object_modified; ?></dd></dl>
						<dl><dt>Status ( Public | Not Public ):</dt> <dd>Public</dd></dl>

					<?php endif; ?>

			</div>
	<div id="citation-info"><h3>Citation Information:</h3>
	<div class="citation"><?php echo $object->getCitation(); ?></div>
	</div>
			<div id="object-tags">
				<h2>Everyone&#8217;s Tags:</h2>
				<ul id="all-tags" class="tags">
				<?php foreach( $object->tags as $tag ): ?>
					<li><a rel="tag" href="<?php echo $_link->to( 'objects', 'all' ); ?>?tags=<?php echo urlencode( $tag['tag_name'] ); ?>"><?php echo htmlentities( $tag['tag_name'] ); ?></a><?php if( $object->tags->nextIsValid() ) echo ','; ?></li>
				<?php endforeach; ?>
				</ul>
				<h3>My Tags:</h3>
				<ul id="my-tags" class="tags">
				<?php foreach( $object->myTags( self::$_session->getUser()->getId() ) as $mytag ): ?>
					<li><a rel="tag" href="<?php echo $_link->to( 'account', 'all' ); ?>?tags=<?php echo urlencode( $mytag['tag_name'] ); ?>"><?php echo htmlentities( $mytag['tag_name'] ); ?></a>  <span class="remove-tag"><a href="javascript:void(0);" onclick="if( confirm( 'Are you sure you want to remove this tag?' ) ){ removeMyTag('<?php echo $mytag['tag_id']; ?>', '<?php echo $object->getId(); ?>', '<?php echo self::$_session->getUser()->getId(); ?>', this ); }">x</a></span></li>
				<?php endforeach; ?>
				</ul>
			</div>

			<div id="add-tags">
				<input type="text" size="20" id="new-tags" />
				<input type="button" value="Add tags" onclick="addTags(document.getElementById('new-tags').value, '<?php echo $object->getId(); ?>');" ></input>
			</div>
			<?php if( $object->location->total() > 0): ?>
			<div id="object-location">
				<h2>Location:</h2>
				<div id="object-map"></div>
			</div>
			<?php endif; ?>
		</div>	
	</div>
</div>
<ul class="object-nav navigation">
	<li class="previous"><?php if ($__c->objects()->getPrevObjectID()): ?><a href="<?php echo $_link->to( 'objects' , 'show' ).$__c->objects()->getPrevObjectID()->object_id; ?>">Previous Item</a><?php endif; ?></li>
	<li class="next"><?php if ($__c->objects()->getNextObjectID()): ?><a href="<?php echo $_link->to( 'objects' , 'show' ).$__c->objects()->getNextObjectID()->object_id; ?>">Next Item</a><?php endif; ?></li>
</ul>