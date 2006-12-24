<?php 
// Layout:default;
	$object = $__c->objects()->findById();
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

			<div id="object">	
				
				<div id="object-sidebar">				
					<?php //if (1==0): // start hiding data?>					
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
					<div id="object-dublin-core">
						<h3>Details:</h3>
						<p><strong>Last Modified:</strong> <span id="object_modified"><?php echo date('d M Y (h:i a \E\S\T)', strtotime($object->object_modified) ); ?></span></p>
						<p><strong>Author:</strong> <span id="object_creator"><?php echo $object->creator->contributor_first_name . ' ' . $object->creator->contributor_last_name; ?></span></p>
						<!--<p><strong>Other Authors:</strong> <span id="object_creator"><?php echo $object->creator_other; ?></span></p>-->
						<p><strong>Language:</strong> <span id="object_language"><?php echo $object->object_language; ?></span></p>
						<p><strong>Rights:</strong> <span id="object_rights"><?php 
						
							switch ($object->object_rights) 
							{
										case 'by-nc-nd': ?>
											Creative Commons <a href="http://creativecommons.org/licenses/by-nc-nd/2.5/" class="popup">Attribution Non-commercial No Derivatives</a>
										<?php break;
										case 'by-nc-sa': ?>
											Creative Commons <a href="http://creativecommons.org/licenses/by-nc-sa/2.5/" class="popup">Attribution Non-commercial Share Alike</a>
										<?php break;
										case 'by-nc': ?>
											Creative Commons <a href="http://creativecommons.org/licenses/by-nc/2.5/" class="popup">Attribution Non-commercial</a>
										<?php break;
										case 'by-nd': ?>
											Creative Commons <a href="http://creativecommons.org/licenses/by-nd/2.5/" class="popup">Attribution No Derivatives</a>
										<?php break;
										case 'by-sa': ?>
											Creative Commons <a href="http://creativecommons.org/licenses/by-sa/2.5/" class="popup">Attribution Share Alike</a>
										<?php break;
										case 'by': ?>
											Creative Commons <a href="http://creativecommons.org/licenses/by/2.5/" class="popup">Attribution</a>
										<?php break;
										case 'publicdomain-2': ?>
											Creative Commons <a href="http://creativecommons.org/license/publicdomain-2" class="popup">Public Domain</a>
										<?php break;
										case 'founderscopyright': ?>
											Creative Commons <a href="http://creativecommons.org/projects/founderscopyright/" class="popup">Founders Copyright</a>
										<?php break;

							}
								
							
						?></span></p>
						<?php if( self::$_session->isAdmin() ): ?>
							<p><strong>Added to Archive:</strong> <span id="object_added"><?php echo $object->object_added; ?></span></p>
							<p><strong>Last Modified On:</strong> <span id="object_added"><?php echo $object->object_modified; ?></span></p>
						<?php endif; ?>
					</div>
	<?php //endif; // end hiding data ?>

					<div id="object-files">
					<?php if( $object->files->total() > 0 ): ?>
						<h3>Files:</h3>
						<?php foreach( $object->files as $file ): ?>
							<?php if( $file->file_thumbnail_name ): ?>
							<a href="<?php echo WEB_VAULT_DIR.DS.$file->file_archive_filename; ?>" class="popup"><?php $_html->thumbnail( $file->file_thumbnail_name,
												array(	'class' => 'thumbnail',
												 		'alt'	=> $file->file_description,
												 		'title'	=> $file->file_title,
												 		'border'=> 1 ),
												100 ); ?></a>
							<?php else: ?>
								<h4><a href="<?php echo WEB_VAULT_DIR.DS.$file->file_archive_filename; ?>" class="popup"><?php echo $file->file_original_filename; ?></a></h4>
							<?php endif; ?>
						<?php endforeach; ?>
					<?php endif; ?>
					</div>

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
				
				
				
				
				<div id="object-main">
					<?php if ($object->object_title != NULL): ?><h3><span id="object_title"><?php echo $object->object_title; ?></span> <?php if (self::$_session->getUser()->user_id == $object->user_id): ?> <span class="editlink">[<a href="<?php echo $_link->to('edit') . $object->object_id; ?>">edit</a>]</span><?php endif; ?></h3><?php endif; ?>

					<?php if ($object->object_description != NULL): ?><p id="object_description"><?php echo nl2br($object->object_description); ?></p><?php endif; ?>

				

								
					<!-- <?php if (1==0): // start hiding data?>					
										<?php $object->getContributor(); ?>			
										<h3>Contributor: <a href="<?php echo $_link->to( 'browse' ); ?>?contributor=<?php echo $object->contributor_id; ?>"><?php echo $object->contributor->contributor_first_name.' '.$object->contributor->contributor_last_name; ?></a></h3>
					<?php endif; // end hiding data ?> -->

					<h3>Citation Information:</h3>
					<p id="cite-as"><?php echo $object->getCitation(); ?></p>
					
					<div class="cc-logo"><a href="http://creativecommons.org/licenses/<?php echo $object->object_rights; ?>/2.5/" class="popup"><img src="http://creativecommons.org/images/public/somerights20.png"></a></div>
					
				</div>

			</div>
