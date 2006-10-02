<?php
// Layout: default;
$object = $__c->objects()->edit();
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

function removeTag( tag_id, object_id )
{
	var opt = {
	    method: 'post',
	    postBody: 'object_id=' + object_id + '&tag_id=' + tag_id,
	    onSuccess: function() {
			new Effect.Fade( 'tag-' + tag_id );
	    },
	    onFailure: function() {
	        alert('Could not delete tag.');
	    }
	}

	new Ajax.Request('<?php echo $_link->to( 'objects', 'ajaxRemoveTag' ); ?>', opt);
}

function deleteFile( file_id )
{
	var opt = {
	    method: 'post',
	    postBody: 'file_id=' + file_id,
	    onSuccess: function() {
			new Effect.Fade( 'file-' + file_id );
	    },
	    onFailure: function() {
	        alert('Could not delete file.');
	    }
	}

	new Ajax.Request('<?php echo $_link->to( 'objects', 'ajaxDeleteFile' ); ?>', opt);
}

function deleteObjectFromCategory( object_id )
{
	var opt = {
	    method: 'post',
	    postBody: 'object_id=' + object_id,
	    onSuccess: function() {
			new Effect.Fade( 'category_form', {duration: 0.6} );
			new Effect.Appear( 'category_add', {duration: 0.6} );
			setTimeout( "document.getElementById('category_form').innerHTML = null", 600 );
	    },
	    onFailure: function() {
	        alert('Could not delete file.');
	    }
	}

	new Ajax.Request('<?php echo $_link->to( 'objects', 'ajaxDeleteObjectFromCategory' ); ?>', opt);	
}
</script>


<?php include( 'subnav.php' ); ?>

<h2>Edit Item #<?php echo $object->object_id; ?>: <?php echo $object->object_title; ?></h2>
<?php  ?>
<form method="post" id="object-addedit" action="<?php echo $_link->to( 'objects', 'edit' ).$object->object_id; ?>" enctype="multipart/form-data">

<?php include( 'form.php' ); ?>

<fieldset>
	<legend>Associated Files</legend>
	<label>These are the associated files with this object</label>
	<ul class="filelist">
	<?php foreach( $object->files as $file ): ?>
		<li id="file-<?php echo $file->getId(); ?>"><a href="javascript:void(0)" onclick="window.open();"><?php echo $file->file_original_filename; ?></a><input type="button" value="X" onclick="if( confirm( 'Are you sure you want to permanently remove this file from the object as well as the archive?' ) ){ deleteFile( '<?php echo $file->getId(); ?>' )}"></li>
	<?php endforeach; ?>
	</ul>

</fieldset>
<input type="submit" value="Edit Object &gt;&gt;" name="object_edit" />

</form>
<form method="post" action="<?php echo $_link->to( 'objects', 'delete'); ?>">
	<input type="hidden" value="<?php echo $object->getId(); ?>" name="object_id" />
	<input type="submit" value="Delete Object &gt;&gt;" name="object_delete" onclick="return confirm( 'Are you sure you want to delete this object, all of it\'s files, tags, and other data from the archive?' );"></input>
</form>