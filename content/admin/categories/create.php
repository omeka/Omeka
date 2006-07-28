<?php
//Layout: default;
$__c->categories()->create();
$saved = self::$_session->getValue( 'category_form_saved' );
?>

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

<form method="post" action="create" onsubmit="if( this.submitted ) return true; else return false;">
<fieldset class="formElement">
	<label for="category_name">Category Name:</label>
	<p class="instructionText">Be descriptive yet concise; no punctuation.</p>
	<?php
		$_form->text(	array(	'size'	=> '20',
									'value'	=> $saved['category']['category_name'],
									'id'	=> 'category_name',
									'name'	=> 'category[category_name]' ) );
	?>
</fieldset>

<fieldset class="formElement">
	<label for="category_description">Category Description:</label>
	<p class="instructionText">Describe the object type and enter any further description/instruction concerning default object metadata.</p>
	<?php 
		$_form->textarea(	array(	'rows'	=> '4',
									'cols'	=> '60',
									'id'	=> 'category_description',
									'name'	=> 'category[category_description]' ),
							 		$saved['category']['category_description'] );
	?>
</fieldset>

<h3>Category Extended Metafield Elements</h3>

<fieldset class="formElement">
	<label for="category_metafield_number">How many extended element fields does the object type need?</label>
	<p class="instructionText">Click 'Get extended fields' after entering a number in the box below.</p>
	<p class="instructionText">Fields not filled in will be ignored.</p>
	<input type="text" name="category_metafield_number" id="category_metafield_number" value="<?php echo htmlentities( $saved['category_metafield_number'] ) ?>" size="3" onkeypress="isEnter(event)" />
	<a href="#" onclick="getExtendedForm()">Get extended fields</a>||<a href="#" onclick="removeExtendedForm()">Remove Extended Fields</a>
	<div id="extended_elements"></div>
</fieldset>

<p>Before adding this category, double check that everything is right.  If it is, continue:</p>
<input type="hidden" name="category_submitted" value="category_submitted"/>
<input type="button" value="Add this Category" id="object_category_form_submit" onclick="this.form.submitted = true; this.form.submit(); return true;" />

</form>