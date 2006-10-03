<?php
// Layout: show;
$__c->items()->add();
$saved = self::$_session->getValue( 'item_form_saved' );
?>

<script type="text/javascript">
var ajax;
function getData(id, form) {
	if( id == '' ) {
		Effect.BlindUp( form, { duration: 0.6 } );
		setTimeout( "document.getElementById('" + form + "').innerHTML = null", 600 );
		return false;
	}
	
	var opt = {
		parameters:'id=' + id,
		method:'get',
		onComplete: function(t) {
			new Effect.BlindDown( form, {duration: 0.8} );
		}
	}
	
	ajax = new Ajax.Updater(form,'<?php echo $_link->to( "items" ); ?>'+form,opt);
}

function addFile() {
	var input = document.createElement("div");
	input.style.display = "none";
	document.getElementById('files').appendChild( input );
	input.innerHTML = 'Attach this file: <input name="itemfile[]" type="file" /><a href="javascript:void(0);" onclick="removeFile( parentNode )">Remove</a>';
	Effect.Appear( input, {duration: 0.4} );
}

function removeFile( node ) {
	Effect.Fade( node, {duration: 0.4} );
	setTimeout( function() { document.getElementById('files').removeChild( node ) }, 600);
}

function showResponse(div) {
	Effect.BlindDown(div);
}
</script>
<ul id="sub-navigation" class="navigation subnav">
	<li<?php if(self::$_route['template'] == 'index') {echo ' class="current"';} ?>><a href="<?php echo $_link->to('items'); ?>">Show Items</a></li>
	<li<?php if(self::$_route['template'] == 'add') {echo ' class="current"';} ?>><a href="<?php echo $_link->to('items', 'add'); ?>">Add Item</a></li>
</ul>

<h2>Add an Item</h2>

<form method="post" id="item-addedit" action="<?php echo $_link->to( 'items', 'add' ); ?>" enctype="multipart/form-data">

<?php include( 'form.php' ); ?>

<input type="submit" value="Insert Item &gt;&gt;" name="item_add" />

</form>