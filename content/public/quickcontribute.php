<?php
// Layout: contribute;
?>
<form id="quickcontribute" method="post" action="<?php echo $_link->to('index'); ?>" >
	<h2>Add Your Memory</h2>
	<fieldset id="chooseaquestion">
	<label for="metadata_story_question">Choose:</label>
	<?php 
	if (isset($saved['metadata']['Story Question'])) $q = @$saved['metadata']['Story Question'];
	else $q = 'How is life different a year later?';
	$_form->select(	array(	'name'	=> 'metadata[Story Question]',
							'id'	=> 'metadata_story_question' ),
					array(	' Tell us about a hurricane hero'			=> 'Tell us about a hurricane hero',
							' Remember someone or something that you lost'	=> ' Remember someone or something that you lost',
							' How is life different a year later?'			=> ' How is life different a year later?',
							' Why did you decide to stay or leave your home?'	=>	'Why did you decide to stay or leave your home?' ,
							' Tell us how you helped others'	=>	' Tell us how you helped others',
							' How did the media coverage make you feel?'	=>	' How did the media coverage make you feel?'),
					$q ); ?>

	</fieldset>
	<fieldset>
	<label class="hide" for="metadata_story_text">Tell us your story</label>
	<textarea name="metadata[Story Text]" id="metadata_story_text" rows="5" cols="30"><?php echo @$saved['metadata']['Story Text']; ?></textarea>
	<label for="contributor_first_name">Your Name</label>
	<input type="text" class="textinput" name="Contributor[contributor_first_name]" id="contributor_first_name" value="<?php echo @$saved['Contributor']['contributor_first_name']; ?>" />
	<input type="text" class="textinput" name="Contributor[contributor_last_name]" id="contributor_last_name" value="<?php echo @$saved['Contributor']['contributor_last_name']; ?>" />
	<?php
		$_form->displayError( 'Contributor', 'contributor_first_name', $__c->objects()->validationErrors() );
		$_form->displayError( 'Contributor', 'contributor_last_name', $__c->objects()->validationErrors() );
	?>
	<label for="contributor_email">Your Email</label>
	<input type="text" class="textinput" name="Contributor[contributor_email]" id="contributor_email" />
	<?php
		$_form->displayError( 'Contributor', 'contributor_email', $__c->objects()->validationErrors() );
	?>
	</fieldset>
	<fieldset>
		<label for="object_contributor_posting">In addition to saving your contribution to the archive, may we post it on this site?</label>					
		<?php
			$_form->select(	array(	'name'	=> 'Object[object_contributor_posting]',
									'id'	=> 'object_contributor_posting' ),
							array(	'yes'			=> 'Yes, including my name',
									'anonymously'	=> 'Yes, but without my name',
									'no'			=> 'No, just save it to the archive' ),
							'yes' );
		?>
	

	</fieldset>
	<fieldset>
	<input type="hidden" name="Object[category_id]" id="object_category_id" value="1" />
	<input type="hidden" name="Object[object_contributor_consent]" id="object_contributor_consent" value="yes" />
	<input type="hidden" name="Contributor[contributor_contact_consent]" id="contributor_contact_consent" value="yes" />
	<input type="hidden" name="Object[object_language]" id="object_language" value="eng" />
	<input type="hidden" name="Object[object_status]" id="object_status" value="review" />
	<input type="submit" name="object_add" id="object_add" value="Add" class="submitinput" />
	
	</fieldset>
	
</form>
