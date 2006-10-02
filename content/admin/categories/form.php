<script type="text/javascript">
var ajax;
function getExtendedForm() {
	num = parseInt(document.getElementById( 'category_metafield_number' ).value);
	if( num > 0 && num != NaN )
	{
		var opt = {
			parameters: 'num_fields=' + num,
			method:'get',
			onComplete: function() {
				new Effect.BlindDown( 'extended_elements', {duration: 0.8} );
			}
		}
	
		ajax = new Ajax.Updater(
			'extended_elements',
			'<?php echo $_link->to('categories', 'ajax_create_form'); ?>',
			opt);
	}
	else if( document.getElementById( 'category_metafield_number' ).value == 0
				|| document.getElementById( 'category_metafield_number' ).value == '' )
	{
		removeExtendedForm();
	}
	else
	{
		alert( 'Please only enter a whole number greater than zero, or zero to clear the extended forms.' )
	}
}

function removeExtendedForm()
{
	Effect.BlindUp( 'extended_elements', { duration: 0.6 } );
	setTimeout( "document.getElementById('extended_elements').innerHTML = null", 600 );
	return false;
}

function isEnter(event)
{
	var keycode = event.keyCode ? event.keyCode :
					event.which ? event.which : event.charCode;
	if( keycode == 13 ) {
		getExtendedForm();
	}
}
</script>
<fieldset>
	<legend>General Type Information</legend>
<label for="category_name">Type Name:</label>
	<p class="instructionText">Be descriptive yet concise; no punctuation.</p>
	<?php
		$_form->text(	array(	'size'	=> '20',
									'value'	=> $category->category_name,
									'id'	=> 'category_name',
									'class' => 'textinput',
									'name'	=> 'category[category_name]' ) );
	?>

<label for="category_description">Type Description:</label>
	<p class="instructionText">Describe the object type and enter any further description/instruction concerning default object metadata.</p>
	<?php 
		$_form->textarea(	array(	'rows'	=> '4',
									'cols'	=> '60',
									'id'	=> 'category_description',
									'name'	=> 'category[category_description]' ),
							 		issetor($saved['category']['category_description'], $category->category_description ));
	?>
</fieldset>
<fieldset>
<legend>Type Extended Metafield Elements</legend>
<?php 
	$i = 0;
	// Get the old metafield names and ids
	$old_mfs = $__c->metafields()->all();

	if ($category): 
	//	print_r($category->metafields); exit;
		foreach ($category->metafields as $meta) {
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
	<label for="category_metafield_number">How many extended element fields does the object type need?</label>
	<p class="instructionText">Click &#8216;Add more fields&#8217; after entering a number in the box below.</p>
	<p class="instructionText">Fields not filled in will be ignored.</p>
	<input type="text" name="category_metafield_number" id="category_metafield_number" value="<?php echo htmlentities( @$saved['category_metafield_number'] ) ?>" size="3" onkeypress="isEnter(event)" />
	<input type="submit" name="add_fields" value="Add more fields -&gt;" />
	<a href="#" onclick="getExtendedForm()">Get extended fields</a>||<a href="#" onclick="removeExtendedForm()">Remove Extended Fields</a>
	<div id="extended_elements"></div>

	<?php if(!empty($more_fields)): 

for($j=$category->metafields->total(); $j<($category->metafields->total()+$more_fields); $j++): ?>
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