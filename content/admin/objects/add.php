<?php
// Layout: default;
$__c->objects()->add();
$saved = self::$_session->getValue( 'object_form_saved' );
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
	
	ajax = new Ajax.Updater(form,'<?php echo $_link->to( "objects" ); ?>'+form,opt);
}

function addFile() {
	var input = document.createElement("div");
	input.style.display = "none";
	document.getElementById('files').appendChild( input );
	input.innerHTML = 'Attach this file: <input name="objectfile[]" type="file" /><a href="javascript:void(0);" onclick="removeFile( parentNode )">Remove</a>';
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

<style type="text/css" media="screen">
	.form-error { color: red; font-size: 1.2em;}
</style>


<?php include( 'subnav.php' ); ?>

<br/>

<h1>Add an Object</h1>

<form method="post" action="<?php echo $_link->to( 'objects', 'add' ); ?>" enctype="multipart/form-data">

<?php include( 'form.php' ); ?>

<input type="submit" value="Insert Object &gt;&gt;" name="object_add" />

</form>