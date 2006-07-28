<?php 
	$object = $__c->objects()->findById();
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $object->object_title; ?> | Katrina's Jewish Voices</title>
<?php include ('inc/metalinks.php'); ?>
<style type="text/css" media="screen">
#object-sidebar {display:block; float:right; width: 252px;}
</style>
<script type="text/javascript" src="/staff/nagrin/jwa/content/common/javascripts/prototype.js"></script>

<script type="text/javascript" src="/staff/nagrin/jwa/content/common/javascripts/scriptaculous.js"></script>
<script type="text/javascript" src="/staff/nagrin/jwa/content/common/javascripts/common.js"></script>
<script type="text/javascript" charset="utf-8">
	//<![CDATA[
	
	addLoadEvent(popUps);
	
	function markFav( object_id )
	{
		var opt = {
			parameters:'id=' + object_id,
			method:'post'
		}
		ajax = new Ajax.Updater('mark-favorite','<?php echo $_link->to( "ajaxMarkFav" ); ?>', opt);
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
		new Ajax.Updater('tags','<?php echo $_link->to('ajaxRemoveMyTag'); ?>', opt );
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
		new Ajax.Request('<?php echo $_link->to('ajaxAddMyTags'); ?>', opt );
	}
    //]]>
</script>
</head>

<body id="browse" class="single">
<a class="hide" href="#content">Skip to Content</a>
<div id="wrap">
<?php include("inc/header.php"); ?>
	<div id="content">
		<h2>Browse</h2>
		<div id="primary">
			<div id="object">	
				<div id="object-main">
					<h3><span id="object_title"><?php echo $object->object_title; ?></span></h3>
					<p id="object_description"><?php echo $object->object_description; ?></p>
			
					
					<?php if( $object->category_id ): ?>
					<h3 id="object-category">Object Type: <span><?php echo $object->category_name; ?></span></h3>
						
					<?php endif; ?>
					
<?php if (1==0): // start hiding data?>					
					<?php $object->getContributor(); ?>			
					<h3>Contributor: <a href="<?php echo $_link->to( 'browse' ); ?>?contributor=<?php echo $object->contributor_id; ?>"><Anne Brener><?php echo $object->contributor->contributor_first_name.' '.$object->contributor->contributor_last_name; ?></a></h3>
<?php endif; // end hiding data ?>
<h3>Citation Information:</h3>
<p id="cite-as"><?php echo $object->getCitation(); ?></p>

</div>
				<div id="object-sidebar">
					<?php if (self::$_session->getUser()): ?>
					<div id="mark-favorite">
						<a href="javascript:void(0)" onclick="markFav('<?php echo $object->getId(); ?>');" >
						<?php if( $object->isFav( self::$_session->getUser()->getId() ) ): ?>
						<img src="<?php echo $_link->in('favorite-on.gif', 'i');?>" border="0" title="Favorite" title="Favorite" />
						<?php else: ?>
						<img src="<?php echo $_link->in('favorite-off.gif', 'i');?>" border="0" title="Mark as favorite" title="Favorite" />
						<?php endif; ?>
						</a>
					</div>
					<?php endif; ?>
					<?php if( $object->files->total() > 0 ): ?>
						<h3>View Files</h3>
						<?php foreach( $object->files as $file ): ?>
							<?php if( $file->file_thumbnail_name ): ?>
							<a href="<?php echo $_link->to('file') . $file->getId(); ?>" class="popup"><?php $_html->thumbnail( $file->file_thumbnail_name,
												array(	'class' => 'thumbnail',
												 		'alt'	=> $file->file_description,
												 		'title'	=> $file->file_title,
												 		'border'=> 1 ),
												100 ); ?></a>
							<?php else: ?>
							<h4><a href="<?php echo $_link->to('file') . $file->getId(); ?>" class="popup"><?php echo $file->file_original_filename; ?></a></h4>
							<?php endif; ?>
						<?php endforeach; ?>
					<?php endif; ?>							
				
			

<?php if (1==0): // start hiding data?>					
					<div id="object-dublin-core">
						<h3>Core Metadata</h3>
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
						<p><strong>Creator:</strong> <span id="object_creator"><?php echo $object->contributor->contributor_first_name . ' ' . $object->contributor->contributor_last_name; ?></span></p>
						<p><strong>Other Creators:</strong> <span id="object_creator"><?php echo $object->creator_other; ?></span></p>
						<p><strong>Publisher:</strong> <span id="object_publisher"><?php echo $object->object_publisher; ?></span></p>
						<p><strong>Language:</strong> <span id="object_language"><?php echo $object->object_language; ?></span></p>
						<p><strong>Relation:</strong> <span id="object_relation"><?php echo $object->object_relation; ?></span></p>
						<p><strong>Rights:</strong> <span id="object_rights"><?php echo $object->object_rights; ?></span></p>
						<?php if( self::$_session->isAdmin() ): ?>
							<p><strong>Added to Archive:</strong> <span id="object_added"><?php echo $object->object_added; ?></span></p>
							<p><strong>Last Modified On:</strong> <span id="object_added"><?php echo $object->object_modified; ?></span></p>
						<?php endif; ?>
					</div>
<?php endif; // end hiding data ?>
						<div id="object-tags">
							<h3>Everyone's Tags:</h3>
							<ul id="tags" class="taglist">
							<?php foreach( $object->tags as $tag ): ?>
								<li><a href="<?php echo $_link->to( 'browse' ); ?>?tags=<?php echo urlencode( $tag['tag_name'] ); ?>"><?php echo htmlentities( $tag['tag_name'] ); ?></a><?php if( $object->tags->nextIsValid() ) echo ','; ?></li>
							<?php endforeach; ?>
							</ul>
							<?php if (self::$_session->getUser()): ?>
							<h3>My Tags:</h3>
							<ul id="my-tags" class="taglist">
								<?php if ($object->myTags(self::$_session->getUser()->getId() ) ): ?>
							<?php foreach( $object->myTags( self::$_session->getUser()->getId() ) as $mytag ): ?>
								<li><a href="<?php echo $_link->to( 'browse' ); ?>?tags=<?php echo urlencode( $mytag['tag_name'] ); ?>"><?php echo htmlentities( $mytag['tag_name'] ); ?></a>  <a href="javascript:void(0);" onclick="if( confirm( 'Are you sure you want to remove this tag?' ) ){ removeMyTag('<?php echo $mytag['tag_id']; ?>', '<?php echo $object->getId(); ?>', '<?php echo self::$_session->getUser()->getId(); ?>', this ); }">[x]</a></li>
							<?php endforeach; ?>
							<?php else: ?>
							<li>You have not tagged this object.</li>
							<?php endif;?>
							</ul>
							<?php endif;?>
						</div>
						<?php if (self::$_session->getUser()): ?>
						<div id="add-tags">
							<input type="text" size="20" id="new-tags" ></input>
							<input type="button" value="Add tags" onclick="addTags(document.getElementById('new-tags').value, '<?php echo $object->getId(); ?>');" ></input>
						</div>
						<?php endif; ?>

					
					<?php if( $object->location->total() > 0): ?>
					<div id="object-location">
						<h2>Location:</h2>
						<div id="object-map"></div>
					</div>
					<?php endif; ?>
				</div>

			</div> <!-- closes object div -->
		</div> <!-- closes primary div -->
	</div> <!-- closes content div -->
	
<?php include("inc/footer.php"); ?>
</div>
</body>
</html>