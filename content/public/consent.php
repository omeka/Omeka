<?php
// Layout: default;
	$__c->objects()->submitContribution();
?>
<?php 	//MASK OLD CONSENT FORM TEXT (INSERT NEW TEXT IF APPLICABLE)
		if(1==0): ?>
			<p>You are being asked to contribute your recollections to the Hurricane  
			Digital Memory Bank, which is developing a permanent digital record  
			of the events surrounding the major hurricanes of 2005. Your  
			participation in this project will allow future historians, and  
			people such as yourself, to gain a greater understanding of these  
			events and the responses to them.</p>

			<p>You must be 13 years of age or older to submit material to us. Your  
			submission of material constitutes your permission for, and consent  
			to, its dissemination and use in connection with the Memory Bank in  
			all media in perpetuity. If you have so indicated on the form, your  
			material will be published on the Memory Bank website (with or  
			without your name, depending on what you have indicated). Otherwise,  
			your response will only be available to approved researchers using  
			the Memory Bank. The material you submit must have been created by  
			you, wholly original, and shall not be copied from or based, in whole  
			or in part, upon any other photographic, literary, or other material,  
			except to the extent that such material is in the public domain.</p>

			<p>By submitting material to the Memory Bank you release, discharge, and  
			agree to hold harmless the Memory Bank and persons acting under its  
			permission or authority, including a public library or archive to  
			which the collection might be donated for purposes of long-term  
			preservation, from any claims or liability arising out the Memory  
			Bank's use of the material, including, without limitation, claims for  
			violation of privacy, defamation, or misrepresentation.</p>

			<p>The Memory Bank has no obligation to use your material.</p>

			<p>You will be sent via email a copy of your contribution to the Memory  
			Bank. We cannot return any material you submit to us so be sure to  
			keep a copy. The Memory Bank will not share your email address or any  
			other information with commercial vendors.</p>
		
<?php endif; ?>
		<h3>Please select your consent below:</h3>
		<form method="post" action="<?php echo $_link->to('consent'); ?>">
		<?php
			$_form->radio( 'object_contributor_consent',
							array(	'yes'		=> 'I Agree. Please include my contribution.',
									'no'		=> 'I do not agree. Please cancel my submission.' ) );
		?>
		<input type="submit" name="submit_object_contributor_consent" value="Continue with submission &gt;&gt;"/>
		</form>
		<p>If you are unclear about any aspect of this consent form, you may contact the Hurricane Digital Memory Bank by email for clarification.</p>