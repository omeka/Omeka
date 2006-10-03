<fieldset>
	<legend>Core Metadata</legend>

	
	<label for="item_public">This item is publicly viewable.
	<input type="checkbox" name="Item[item_public]" id="item_public" value="1" <?php echo (@$saved['Item']['item_public']) ? 'checked="checked"' : '';?>></label>
	
	<label class="readonly" for="item_id">Item's Database ID (read only)</label>

	<?php
		$_form->text(	array(	'name'	=> 'Item[item_id]',
								'id'	=> 'item_id',
								'readonly' => 'readonly',
								'value' => @$saved['Item']['item_id'],
								'class'		=> 'textinput' ) );
	?>

	
	<label for="item_title">Title</label>
	<?php
		$_form->text(	array(	'name'		=> 'Item[item_title]',
								'id'		=> 'item_title',
								'class'		=> 'textinput',
								'size'		=> 40,
								'value'		=> @$saved['Item']['item_title'] ) );

		$_form->displayError( 'Item', 'item_title', $__c->items()->validationErrors() );
	?>

	<label for="item_creator">Creator</label>

	<?php
		$_form->text(	array(	'size'	=> 20,
								'name'	=> 'Item[item_creator]',
								'id'	=> 'item_creator',
								'class'		=> 'textinput',
								'value'	=> @$saved['Item']['item_creator'] ) );
	?>


	<label for="item_subject">Subject</label>
	<p class="instructionText"></p>
	<?php
		$_form->text(	array(	'name'		=> 'Item[item_subject]',
								'id'		=> 'item_subject',
								'class'		=> 'textinput',
								'size'		=> 40,
								'value'		=> @$saved['Item']['item_subject'] ) );

		$_form->displayError( 'Item', 'item_subject', $__c->items()->validationErrors() );
	?>



	<label for="item_description">Description</label>
	<?php 
		$_form->textarea( 	array(	'rows'	=> '8',
									'cols'	=> '60',
									'id'	=> 'item_description',
									'name'	=> 'Item[item_description]' ),
									@$saved['Item']['item_description'] );
	?>

	<label for="item_publisher">Publisher</label>
	<p>An entity responsible for making the resource available.</p>
	<?php 
		$_form->text( 	array(	'size'	=> 40,
								'name'	=> 'Item[item_publisher]',
								'class'		=> 'textinput',
								'id'	=> 'item_publisher',
								'value'	=> @$saved['Item']['item_publisher'] )
								 );
	?>

	<label for="item_creator_other">Other Creator</label>
	
	<?php
		$_form->text( array(	'name'		=> 'Item[item_additional_creator]',
		 						'id'		=> 'item_additional_creator',
								'class'		=> 'textinput',
								'value'		=> @$saved['Item']['item_additional_creator'],
								'size'		=> 40,
								) );
	?>
		<?php
		$_form->displayError( 'Item', 'item_date', $__c->items()->validationErrors() );
	?>


	<label for="item_date">Date</label>
	
	<?php
		$_form->text( array(	'name'		=> 'Item[item_date]',
		 						'id'		=> 'item_date',
								'class'		=> 'textinput',
								'value'		=> @$saved['Item']['item_date'],
								'size'		=> 40,
								) );
	?>
		<?php
		$_form->displayError( 'Item', 'item_date', $__c->items()->validationErrors() );
	?>
	<label for="item_source">Source</label>
	<?php
		$_form->textarea( 	array(	'rows'	=> '8',
									'cols'	=> '60',
									'id'	=> 'item_source',
									'name'	=> 'Item[item_source]' ),
									@$saved['Item']['item_source'] );
	?>


		<?php if(1==0):?>


		<label for="item_language">Language</label>
		<?php
			$_form->radio(	'Item[item_language]',
							array( 'eng' => 'English', 'fra' => 'French', 'other' => 'Other' ),
							'eng',
							@$saved['Item']['item_language'] );

			$_form->text(	array(	'name'	=> 'item_language_other',
									'class'		=> 'textinput',
			 						'value'	=> @$saved['item_language_other'] ) );
		?>
		<?php else:?>
		<?php $_form->hidden( array(	'id'	=> 'item_language', 
										'name'	=> 'Item[item_language]', 
										'value'	=>	'eng' ) );?>
		<?php endif;?>








		<label for="item_relation">Relation</label>
		<p></p>
		<?php
			$_form->textarea( 	array(	'rows'	=> '8',
										'cols'	=> '60',
										'id'	=> 'item_relation',
										'name'	=> 'Item[item_relation]' ),
										@$saved['Item']['item_relation'] );
		?>





		<label for="item_coverage">Coverage</label>
			<?php
			$_form->textarea( 	array(	'rows'	=> '8',
										'cols'	=> '60',
										'id'	=> 'item_coverage',
										'name'	=> 'Item[item_coverage]' ),
										@$saved['Item']['item_coverage'] );
			?>
			<label for="item_rights">Rights</label>
			<p>Rights binding the item or legal conditions pertaining to the item.</p>
			<?php
				$_form->textarea( 	array(	'rows'	=> '8',
											'cols'	=> '60',
											'id'	=> 'item_rights',
											'name'	=> 'Item[item_rights]' ),
											@$saved['Item']['item_rights'] );
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
	<label for="type_id">Item Type</label>
	<?php
		$_form->select( array(	'name'		=> 'Item[type_id]',
								'id'		=> 'type_id',
								'onchange'	=> 'getData(this.value, "ajax_type_form")' ),
						$__c->types()->all( 'array' ),
						@$saved['Item']['type_id'],
						'type_id',
						'type_name' );
	?>

<div id="ajax_type_form"><?php

if (@$saved['Item']['type_id']) {
	$cat = $__c->types()->findById(@$saved['Item']['type_id']); ?> 
	<h3>Type: <?php echo $cat->type_name ?></h3>
	
		<h4>Description:</h4>
		<p><?php echo $cat->type_description; ?></p>

		<?php $i=0; foreach( $cat->metafields as $metafield ): ?>
			
			<label><?php echo $metafield->metafield_name ?></label>
			<p><?php echo $metafield->metafield_description ?></p>
			<input type="hidden" name="metadata[<?php echo $i; ?>][metafield_id]" value="<?php echo $metafield->metafield_id; ?>" />
			<?php
				$_form->hidden( array('name' => 'metadata['.$i.'][metatext_id]', 'value' => @$saved['Item']['type_metadata'][$metafield->metafield_id]['metatext_id']) );
			?>
			<textarea rows="4" cols="30" name="metadata[<?php echo $i; ?>][metatext_text]"><?php echo @$saved['Item']['type_metadata'][$metafield->metafield_id]['metatext_text']?></textarea>
			
		<?php $i++; endforeach; ?>
	
<?	} ?></div>
</fieldset>

<fieldset>
	<legend>Item History</legend>
	<label class="readonly" for="item_added">Date item added (read only)</label>

	<?php
		$_form->text(	array(	'name'	=> 'Item[item_added]',
								'id'	=>	'item_added',
								'readonly' => 'readonly',
								'value' => @$saved['item_added'],
								'class'		=> 'textinput' ) );
	?>


	<label class="readonly" for="item_modified">Date item modified (read only)</label>

	<?php
		$_form->text(	array(	'name'	=> 'Item[item_modified]',
								'id' 	=> 'item_modified',
								'readonly' => 'readonly',
								'value' => @$saved['item_modified'],
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