<?php
$num_fields = self::$_request->getProperty( 'num_fields' );

// Get the old metafield names and ids
$old_mfs = $__c->metafields()->all();

// Saved form data
$saved = self::$_session->getValue( 'type_form_saved' );

for($i=0; $i<$num_fields; $i++): ?>
<fieldset class="formElement">
	<label for="metafields[<?php echo $i; ?>][metafield_name]"><?php echo $i + 1 ?>) Extended Element Field Name</label>
	<p class="instructionText">Choose an earlier defined meta field or create your own:</p>
	<select name="metafields[<?php echo $i; ?>][metafield_name]" id="metafields[<?php echo $i; ?>][metafield_name]">
		<option value="">Select a meta field</option>
		<?php foreach( $old_mfs as $metafield ): ?>
		<option value="<?php echo $metafield->metafield_name; ?>"<?php if( $metafield->metafield_name == $saved['metafields'][$i]['metafield_name']) echo ' selected '; ?>><?php echo htmlentities( $metafield->metafield_name ); ?></option>
		<?php endforeach; ?>
	</select>
	<p class="instructionText">You may add your own meta field name here:</p>
	<?php
		$_form->text(array(	'size'	=> '20',
									'value'	=> issetor( $saved['metafields'][$i]['metafield_name_new'], null ),
									'name'	=> 'metafields['.$i.'][metafield_name_new]') );
	?>
	<br />
</fieldset>

<fieldset class="formElement">
	<label for="metafields[<?php echo $i; ?>][metafield_description]">Description of Field Name</label>
	<p class="instructionText">Please describe the metafield, it's contents, and / or reason for existing:</p>
	<p class="instructionText">If using a predefined metafield name, you do not need to fill this out.</p>
	<?php 
		$_form->textarea( 	array(	'rows'	=> '4',
									'cols'	=> '60',
									'id'	=> 'metafields['.$i.'][metafield_description]',
									'name'	=> 'metafields['.$i.'][metafield_description]'),
									issetor( $saved['metafields'][$i]['metafield_description'], null ) );
	?>
</fieldset>

<?php endfor; ?>