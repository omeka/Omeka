<fieldset>
	<legend>General Type Information</legend>
<label for="type_name">Type Name:</label>
	<?php /*<p class="instructionText">Be descriptive yet concise; no punctuation.</p> */ ?>
	<?php
		$_form->text(	array(	'size'	=> '20',
									'value'	=> issetor($saved['type']['type_name'], @$type->type_name ),
									'id'	=> 'type_name',
									'class' => 'textinput',
									'name'	=> 'type[type_name]' ) );
	?>

<label for="type_description">Type Description:</label>
	<?php /*<p class="instructionText">Describe the item type and enter any further description/instruction concerning default item metadata.</p> */ ?>
	<?php 
		$_form->textarea(	array(	'rows'	=> '4',
									'cols'	=> '60',
									'id'	=> 'type_description',
									'name'	=> 'type[type_description]' ),
							 		issetor($saved['type']['type_description'], @$type->type_description ));
	?>
</fieldset>
<fieldset>
<legend>Type Extended Metafield Elements</legend>
<?php 
	$i = 0;
	// Get the old metafield names and ids
	$old_mfs = $__c->metafields()->all();

	if (@$type): 
	//	print_r($type->metafields); exit;
		foreach ($type->metafields as $meta) {
?>

		<input type="hidden" name="metafields[<?php echo $i; ?>][metafield_id]" value="<?php echo $meta->metafield_id; ?>" />
		<label for="metafields[<?php echo $i; ?>][metafield_name]"><?php echo $i + 1; ?>) Extended Element Field Name</label>
		<p class="instructionText">Choose an earlier defined meta field or create your own:</p>
		<select name="metafields[<?php echo $i; ?>][metafield_name]" id="metafields[<?php echo $i; ?>][metafield_name]">
			<option value="">Select a meta field</option>
			<?php foreach( $old_mfs as $metafield ): ?>
			<option value="<?php echo $metafield->metafield_name; ?>"<?php if( $metafield->metafield_name == $meta->metafield_name) echo ' selected '; ?>><?php echo htmlentities( $metafield->metafield_name ); ?></option>
			<?php endforeach; ?>
		</select>
		<p class="instructionText">You may add your own meta field name here:</p>
		<?php
			$_form->text(array(	'size'	=> '20',
										'value'	=> issetor( $saved['metafields'][$i]['metafield_name_new'], null ),
										'name'	=> 'metafields['.$i.'][metafield_name_new]') );
		?>

		<?php $i++;?>
<?php } endif; ?>
	<label for="type_metafield_number">How many extended element fields does the item type need?</label>
	<p class="instructionText">Click &#8216;Add more fields&#8217; after entering a number in the box below.</p>
	<p class="instructionText">Fields not filled in will be ignored.</p>
	<input type="text" name="type_metafield_number" id="type_metafield_number" value="<?php echo htmlentities( @$saved['type_metafield_number'] ) ?>" size="3" />
	<input type="submit" name="add_fields" value="Add more fields -&gt;" />
	<div id="extended_elements"></div>

	<?php if($more_fields = self::$_request->getProperty('type_metafield_number') ): 
				$total_fields = ( !empty($type) ) ? $type->metafields->total() : 0;
for($j=$total_fields; $j<($total_fields+$more_fields); $j++): ?>
	<label for="metafields[<?php echo $j; ?>][metafield_name]"><?php echo $j + 1 ?>) Extended Element Field Name</label>
	<p class="instructionText">Choose an earlier defined meta field or create your own:</p>
	<select name="metafields[<?php echo $j; ?>][metafield_name]" id="metafields[<?php echo $j; ?>][metafield_name]">
		<option value="">Select a meta field</option>
		<?php foreach( $old_mfs as $metafield ): ?>
		<option value="<?php echo $metafield->metafield_name; ?>"<?php if( $metafield->metafield_name == @$saved['metafields'][$j]['metafield_name']) echo ' selected '; ?>><?php echo htmlentities( $metafield->metafield_name ); ?></option>
		<?php endforeach; ?>
	</select>
	<label for="metafield_name_new">You may add your own meta field name here:</label>
	<?php
		$_form->text(array(	'size'	=> '20',
									'value'	=> issetor( $saved['metafields'][$j]['metafield_name_new'], null ),
									'class' => 'textinput',
									'id' => 'metafield_name_new',
									'name'	=> 'metafields['.$j.'][metafield_name_new]') );
	?>
	
	<label for="metafields[<?php echo $j; ?>][metafield_description]">Description of Field Name</label>
	<p class="instructionText">Please describe the metafield, it's contents, and / or reason for existing:</p>
	<p class="instructionText">If using a predefined metafield name, you do not need to fill this out.</p>
	<?php 
		$_form->textarea( 	array(	'rows'	=> '4',
									'cols'	=> '60',
									'id'	=> 'metafields['.$j.'][metafield_description]',
									'name'	=> 'metafields['.$j.'][metafield_description]'),
									issetor( $saved['metafields'][$j]['metafield_description'], null ) );
	?>
<?php endfor; endif;?>
</fieldset>