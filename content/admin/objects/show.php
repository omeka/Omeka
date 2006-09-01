<?php
// Layout: show;
$object = $__c->objects()->findById();
include( 'subnav.php' );
?>

<script src="http://maps.google.com/maps?file=api&amp;v=2.x&amp;key=<?php echo GMAPS_KEY;?>" type="text/javascript"></script>

<script type="text/javascript" charset="utf-8">
	//<![CDATA[
	
	addLoadEvent(popUps);
	
	var map;
	function load()
	{
		<?php if( $object->location->total() > 0 ): ?>
			if (GBrowserIsCompatible())
			{
				mapdiv = document.getElementById("object-map");
				mapdiv.style.width = "100%";
				mapdiv.style.height = "240px";
				map = new GMap2(mapdiv);
				
				var point = new GLatLng(<?php echo $object->location->latitude; ?>, <?php echo $object->location->longitude; ?>);
				
				map.setCenter(point, 13);
				map.addControl(new GSmallMapControl());

				var marker = new GMarker(point);

				  GEvent.addListener(marker, "click", function() {
				    marker.openInfoWindowHtml("Address: <?php echo $object->location->cleanAddress; ?><br/>Zipcode: <?php echo $object->location->zipcode; ?>" );
				  });

				map.addOverlay( marker );

			}
		<?php endif; ?>
	}
	
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

<style type="text/css" media="screen">
/* <![CDATA[ */
	#object {margin: 20px 0 20px 20px; display:block; float:left;}
	#object-main {width: 370px; float: left; margin-right: 30px;}
	#object-main h2 { font-weight:normal; margin-bottom: 10px; color: #36c;}
	#object_description {background-color:#eee; padding: 20px; }
	#cite-as {font-size: 1.2em; line-height: 1.75em; margin-bottom: 10px;}
	#object_title {font-weight:normal; font-size: 2em;}
	
	#object-sidebar {width:256px; float: left; }
	#object-sidebar h2 {margin-bottom:10px;background: #888; color: #fff;display:block; margin:0 0 10px 0;padding: 4px 8px; font-size: 1.4em; font-weight:normal;}
	
	#object-other-meta	h2 {background: #eee; font-size: 1.2em; font-weight:normal; margin: 10px 0; color: #36c;}
	
	#object-category span {color: #555; font-weight:bold;}
	ul.metadata {list-style:square; color: #eee;margin-left: 8px; margin-bottom: 10px;}
	ul.metadata li {padding: 8px 2px 7px 2px; margin-left: 8px;border-bottom: 1px solid #ccc; color: #ccc;}
	ul.metadata li span {color:#555;}
	#object-dublin-core { border: 1px solid #eee; background:#eee;}
	#object-dublin-core p { border-bottom: 1px dotted #BDBDBD; padding-bottom:6px; margin-bottom:6px; font-size:1.1em;margin-left: 8px; margin-right:8px;}
	
	#mark-fav {text-align:right;}
	
	#object-tags {padding:4px;}
	#tags li { display:inline; }
	#my-tags li {list-style:none;}
	
/* ]]> */
</style>

<div id="object">
		
	<ul class="object-nav">
		<li class="previous"><?php if ($__c->objects()->getPrevObjectID()): ?><a href="<?php echo $_link->to( 'objects' , 'show' ).$__c->objects()->getPrevObjectID()->object_id; ?>">Previous object</a><?php endif; ?></li>
		<li class="next"><?php if ($__c->objects()->getNextObjectID()): ?><a href="<?php echo $_link->to( 'objects' , 'show' ).$__c->objects()->getNextObjectID()->object_id; ?>">Next object</a><?php endif; ?></li>
	</ul>
	
	<div id="object-main">
		<h2 id="object_title"><?php echo $object->object_title; ?></h2>
		<div id="cite-as">Citation Information: <?php echo $object->getCitation(); ?></div>
		<p id="object_description">
			<?php 
			if( !$object->object_description )
			{
				echo 'No description given.';
			}
			else
			{
				echo nl2br( $object->object_description );
			}
			?>
		</p>
		<?php if( $object->category_id ): ?>
		<h2 id="object-category">Object Type: <span><?php echo $object->category_name; ?></span></h2>
		<h2>Object Type Metadata:</h2>
			<?php if( $object->category_metadata ): ?>
				<ul class="metadata">
				<?php foreach( $object->category_metadata as $k => $v ): ?>
					<li><span><strong><?php echo $v['metafield_name']; ?>:</strong><br/><?php echo nl2br( $v['metatext_text'] ); ?></span><!--<?php switch ($v['metafield_id']) {
						case '28': // Resolution.
						case '15': // Moving Image Resolution.
							echo 'dpi'; 
							break;
						case '29': // Still Image Width.
						case '30': // Still Image Height.
						case '17': // Moving Image Width.
						case '18': // Moving Image Height.
							echo 'px';
							break;
						case '25': // Bit Depth.
							echo 'bit';
							break;
						} 
						 ?>--></li>
				<?php endforeach; ?>
				</ul>
			<?php endif;?>
		<?php endif; ?>
	</div>

	<div id="object-sidebar">
		<div id="mark-favorite">
			<a href="javascript:void(0)" onclick="markFav('<?php echo $object->getId(); ?>');" >
			<?php if( $object->isFav( self::$_session->getUser()->getId() ) ): ?>
			<img src="<?php echo $_link->in('favorite-on.gif', 'images');?>" border="0" title="Favorite" title="Favorite" />
			<?php else: ?>
			<img src="<?php echo $_link->in('favorite-off.gif', 'images');?>" border="0" title="Mark as favorite" title="Favorite" />
			<?php endif; ?>
			</a>
		</div>
		<div id="mark-featured">
			<a href="javascript:void(0)" onclick="markFeatured('<?php echo $object->getId(); ?>');" >
			<?php if( $object->isFeatured( self::$_session->getUser()->getId() ) ): ?>
			<img src="<?php echo $_link->in('featured-on.gif', 'images');?>" border="0" title="Featured" title="Featured" />
			<?php else: ?>
			<img src="<?php echo $_link->in('featured-off.gif', 'images');?>" border="0" title="Mark as featured" alt="Mark as featured" />
			<?php endif; ?>
			</a>
		</div>
		<div id="object-dublin-core">
			<h2>Core Metadata</h2>
			<p><strong>Date:</strong> <span id="object_date"><?php echo $object->object_date ?></span></p>
			<p><strong>Coverage:</strong> <span id="object_coverage">
				<?php
					if( $object->object_coverage_start && $object->object_coverage_end )
					{
						echo $object->object_coverage_start . '-' . $object->object_coverage_end;
					}
					elseif( $object->object_coverage_start && !$object->object_coverage_end )
					{
						echo $object->object_coverage_start . '- On Going';
					}
				?>
			</span></p>
			<?php if ($object->creator_other): ?><p><strong>Creator:</strong> <span id="object_creator"><?php echo $object->creator_other; ?></span></p><?php endif;?>
			<p><strong>Publisher:</strong> <span id="object_publisher"><?php echo $object->object_publisher; ?></span></p>
			<p><strong>Language:</strong> <span id="object_language"><?php echo $object->object_language; ?></span></p>
			<p><strong>Relation:</strong> <span id="object_relation"><?php echo $object->object_relation; ?></span></p>
			<p><strong>Rights:</strong> <span id="object_rights"><?php echo $object->object_rights; ?></span></p>
			<?php if( self::$_session->isAdmin() ): ?>
				<p><strong>Added to Archive:</strong> <span id="object_added"><?php echo $object->object_added; ?></span></p>
				<p><strong>Status:</strong> <span id="object_added"><?php echo $object->object_status; ?></span></p>
				<p><strong>Last Modified On:</strong> <span id="object_added"><?php echo $object->object_modified; ?></span></p>
			<?php endif; ?>
		</div>
		
		<div id="object-other-meta">
		<?php if( $object->contributor->total() > 0 ): ?>
		<h2>Contributor: <a href="<?php echo $_link->to( 'contributors', 'show' ) . $object->contributor->contributor_id; ?>/"><?php echo $object->contributor->contributor_first_name; ?> <?php echo $object->contributor->contributor_last_name; ?></a></h2>
		<h2>Consent: <?php echo $object->object_contributor_consent; ?></h2>
		<h2>Post on Public Site: <?php echo $object->object_contributor_posting; ?></h2>
		<?php endif; ?>
		
		<?php if( $object->collection_id ): ?>
		<h2>Collection: <a href="<?php echo $_link->to( 'objects', 'all' ) . '?collection='.$object->collection_id; ?>"><?php echo $__c->collections()->findById( $object->collection_id )->collection_name;?></a></h2>
		<?php endif; ?>

		<?php if( $object->files->total() > 0 ): ?>
			<h2>Files:</h2>
			<?php foreach( $object->files as $file ): ?>
				<?php if( $file->file_thumbnail_name ): ?>
				<a href="<?php echo $_link->to('files', 'show') . $file->getId(); ?>" class="popup"><?php $_html->thumbnail( $file->file_thumbnail_name,
									array(	'class' => 'thumbnail',
									 		'alt'	=> $file->file_description,
									 		'title'	=> $file->file_title,
									 		'border'=> 1 ),
									100 ); ?></a>
				<?php else: ?>
					<h4><a href="<?php echo $_link->to('files', 'show') . $file->getId(); ?>" class="popup"><?php echo $file->file_original_filename; ?></a></h4>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>
		
	
			<div id="object-tags">
				<h2>Everyone's Tags:</h2>
				<ul id="tags">
				<?php foreach( $object->tags as $tag ): ?>
					<li><a href="<?php echo $_link->to( 'objects', 'all' ); ?>?tags=<?php echo urlencode( $tag['tag_name'] ); ?>"><?php echo htmlentities( $tag['tag_name'] ); ?></a><?php if( $object->tags->nextIsValid() ) echo ','; ?></li>
				<?php endforeach; ?>
				</ul>
				<h2>My Tags:</h2>
				<ul id="my-tags">
				<?php foreach( $object->myTags( self::$_session->getUser()->getId() ) as $mytag ): ?>
					<li><a href="<?php echo $_link->to( 'account', 'all' ); ?>?tags=<?php echo urlencode( $mytag['tag_name'] ); ?>"><?php echo htmlentities( $mytag['tag_name'] ); ?></a>  <a href="javascript:void(0);" onclick="if( confirm( 'Are you sure you want to remove this tag?' ) ){ removeMyTag('<?php echo $mytag['tag_id']; ?>', '<?php echo $object->getId(); ?>', '<?php echo self::$_session->getUser()->getId(); ?>', this ); }">[x]</a></li>
				<?php endforeach; ?>
				</ul>
			</div>
	
		
		
			<div id="add-tags">
				<input type="text" size="20" id="new-tags" ></input>
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
<br class="clear"/>