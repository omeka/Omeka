<fieldset>
	<legend>Core Metadata</legend>

	
	<label for="object_public">This object is publicly viewable.
	<input type="checkbox" name="Object[object_public]" id="object_public" value="1" <?php echo (@$saved['Object']['object_public']) ? 'checked="checked"' : '';?>></label>
	
	<label class="readonly" for="object_id">Object's Database ID (read only)</label>

	<?php
		$_form->text(	array(	'name'	=> 'Object[object_id]',
								'id'	=> 'object_id',
								'readonly' => 'readonly',
								'value' => @$saved['Object']['object_id'],
								'class'		=> 'textinput' ) );
	?>

	
	<label for="object_title">Title</label>
	<?php
		$_form->text(	array(	'name'		=> 'Object[object_title]',
								'id'		=> 'object_title',
								'class'		=> 'textinput',
								'size'		=> 40,
								'value'		=> @$saved['Object']['object_title'] ) );

		$_form->displayError( 'Object', 'object_title', $__c->objects()->validationErrors() );
	?>

	<label for="object_creator">Creator</label>

	<?php
		$_form->text(	array(	'size'	=> 20,
								'name'	=> 'Object[object_creator]',
								'id'	=> 'object_creator',
								'class'		=> 'textinput',
								'value'	=> @$saved['Object']['object_creator'] ) );
	?>


	<label for="object_subject">Subject</label>
	<p class="instructionText"></p>
	<?php
		$_form->text(	array(	'name'		=> 'Object[object_subject]',
								'id'		=> 'object_subject',
								'class'		=> 'textinput',
								'size'		=> 40,
								'value'		=> @$saved['Object']['object_subject'] ) );

		$_form->displayError( 'Object', 'object_subject', $__c->objects()->validationErrors() );
	?>



	<label for="object_description">Description</label>
	<?php 
		$_form->textarea( 	array(	'rows'	=> '8',
									'cols'	=> '60',
									'id'	=> 'object_description',
									'name'	=> 'Object[object_description]' ),
									@$saved['Object']['object_description'] );
	?>

	<label for="object_publisher">Publisher</label>
	<p>An entity responsible for making the resource available.</p>
	<?php 
		$_form->text( 	array(	'size'	=> 40,
								'name'	=> 'Object[object_publisher]',
								'class'		=> 'textinput',
								'id'	=> 'object_publisher',
								'value'	=> @$saved['Object']['object_publisher'] )
								 );
	?>

	<label for="object_creator_other">Other Creator</label>
	
	<?php
		$_form->text( array(	'name'		=> 'Object[object_additional_creator]',
		 						'id'		=> 'object_additional_creator',
								'class'		=> 'textinput',
								'value'		=> @$saved['Object']['object_additional_creator'],
								'size'		=> 40,
								) );
	?>
		<?php
		$_form->displayError( 'Object', 'object_date', $__c->objects()->validationErrors() );
	?>


	<label for="object_date">Date</label>
	
	<?php
		$_form->text( array(	'name'		=> 'Object[object_date]',
		 						'id'		=> 'object_date',
								'class'		=> 'textinput',
								'value'		=> @$saved['Object']['object_date'],
								'size'		=> 40,
								) );
	?>
		<?php
		$_form->displayError( 'Object', 'object_date', $__c->objects()->validationErrors() );
	?>
	<label for="object_source">Source</label>
	<?php
		$_form->textarea( 	array(	'rows'	=> '8',
									'cols'	=> '60',
									'id'	=> 'object_source',
									'name'	=> 'Object[object_source]' ),
									@$saved['Object']['object_source'] );
	?>


		<?php if(1==0):?>


		<label for="object_language">Language</label>
		<?php
			$_form->radio(	'Object[object_language]',
							array( 'eng' => 'English', 'fra' => 'French', 'other' => 'Other' ),
							'eng',
							@$saved['Object']['object_language'] );

			$_form->text(	array(	'name'	=> 'object_language_other',
									'class'		=> 'textinput',
			 						'value'	=> @$saved['object_language_other'] ) );
		?>
		<?php else:?>
		<?php $_form->hidden( array(	'id'	=> 'object_language', 
										'name'	=> 'Object[object_language]', 
										'value'	=>	'eng' ) );?>
		<?php endif;?>








		<label for="object_relation">Relation</label>
		<p></p>
		<?php
			$_form->textarea( 	array(	'rows'	=> '8',
										'cols'	=> '60',
										'id'	=> 'object_relation',
										'name'	=> 'Object[object_relation]' ),
										@$saved['Object']['object_relation'] );
		?>





		<label for="object_coverage">Coverage</label>
			<?php
			$_form->textarea( 	array(	'rows'	=> '8',
										'cols'	=> '60',
										'id'	=> 'object_coverage',
										'name'	=> 'Object[object_coverage]' ),
										@$saved['Object']['object_coverage'] );
			?>
			<label for="object_rights">Rights</label>
			<p>Rights binding the object or legal conditions pertaining to the object.</p>
			<?php
				$_form->textarea( 	array(	'rows'	=> '8',
											'cols'	=> '60',
											'id'	=> 'object_rights',
											'name'	=> 'Object[object_rights]' ),
											@$saved['Object']['object_rights'] );
			?>



		<label for="tags">Tags</label>
		<p class="instructionText">Words or phrases, separated by commas. (eg. katrina, south port, levy)</p>
		<?php
			$_form->textarea( 	array(	'rows'	=> '2',
										'cols'	=> '60',
										'id'	=> 'tags',
										'name'	=> 'tags' ),
										@$saved['tags'] );
		?>
</fieldset>
<fieldset>
	<legend>Format Metadata</legend>
	<label for="category_id">Object Type</label>
	<?php
		$_form->select( array(	'name'		=> 'Object[category_id]',
								'id'		=> 'category_id',
								'onchange'	=> 'getData(this.value, "ajax_category_form")' ),
						$__c->categories()->all( 'array' ),
						@$saved['Object']['category_id'],
						'category_id',
						'category_name' );
	?>

<div id="ajax_category_form"><?php

if (@$saved['Object']['category_id']) {
	$cat = $__c->categories()->findById(@$saved['Object']['category_id']); ?> 
	<h3>Category: <?php echo $cat->category_name ?></h3>
	
		<h4>Description:</h4>
		<p><?php echo $cat->category_description; ?></p>

		<?php $i=0; foreach( $cat->metafields as $metafield ): ?>
			
			<label><?php echo $metafield->metafield_name ?></label>
			<p><?php echo $metafield->metafield_description ?></p>
			<input type="hidden" name="metadata[<?php echo $i; ?>][metafield_id]" value="<?php echo $metafield->metafield_id; ?>" />
			<?php
				$_form->hidden( array('name' => 'metadata['.$i.'][metatext_id]', 'value' => @$saved['Object']['category_metadata'][$metafield->metafield_id]['metatext_id']) );
			?>
			<textarea rows="4" cols="30" name="metadata[<?php echo $i; ?>][metatext_text]"><?php echo @$saved['Object']['category_metadata'][$metafield->metafield_id]['metatext_text']?></textarea>
			
		<?php $i++; endforeach; ?>
	
<?	} ?></div>
</fieldset>

<fieldset>
	<legend>Object History</legend>
	<label class="readonly" for="object_added">Date object added (read only)</label>

	<?php
		$_form->text(	array(	'name'	=> 'Object[object_added]',
								'id'	=>	'object_added',
								'readonly' => 'readonly',
								'value' => @$saved['object_added'],
								'class'		=> 'textinput' ) );
	?>


	<label class="readonly" for="object_modified">Date object modified (read only)</label>

	<?php
		$_form->text(	array(	'name'	=> 'Object[object_modified]',
								'id' 	=> 'object_modified',
								'readonly' => 'readonly',
								'value' => @$saved['object_modified'],
								'class'		=> 'textinput' ) );
	?>
</fieldset>

<fieldset>
	<legend>Files</legend>
<!-- MAX_FILE_SIZE must precede the file input field -->
<input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
<!-- Name of input element determines name in $_FILES array -->
<div id="files"></div>
<p><a href="javascript:void(0);" onclick="addFile()">Attach a file</a></p>
</fieldset>