<?php
// Layout: default;
$item = $__c->items()->edit();
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

function removeTag( tag_id, item_id )
{
	var opt = {
	    method: 'post',
	    postBody: 'item_id=' + item_id + '&tag_id=' + tag_id,
	    onSuccess: function() {
			new Effect.Fade( 'tag-' + tag_id );
	    },
	    onFailure: function() {
	        alert('Could not delete tag.');
	    }
	}

	new Ajax.Request('<?php echo $_link->to( 'items', 'ajaxRemoveTag' ); ?>', opt);
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

	new Ajax.Request('<?php echo $_link->to( 'items', 'ajaxDeleteFile' ); ?>', opt);
}

function deleteItemFromType( item_id )
{
	var opt = {
	    method: 'post',
	    postBody: 'item_id=' + item_id,
	    onSuccess: function() {
			new Effect.Fade( 'type_form', {duration: 0.6} );
			new Effect.Appear( 'type_add', {duration: 0.6} );
			setTimeout( "document.getElementById('type_form').innerHTML = null", 600 );
	    },
	    onFailure: function() {
	        alert('Could not delete file.');
	    }
	}

	new Ajax.Request('<?php echo $_link->to( 'items', 'ajaxDeleteItemFromType' ); ?>', opt);	
}
</script>


<?php include( 'subnav.php' ); ?>

<h2>Edit Item #<?php echo $item->item_id; ?>: <?php echo $item->item_title; ?></h2>
<?php  ?>
<form method="post" id="item-addedit" action="<?php echo $_link->to( 'items', 'edit' ).$item->item_id; ?>" enctype="multipart/form-data">

<?php include( 'form.php' ); ?>

<fieldset>
	<legend>Associated Files</legend>
	<label>These are the associated files with this item</label>
	<ul class="filelist">
	<?php foreach( $item->files as $file ): ?>
		<li id="file-<?php echo $file->getId(); ?>"><a href="javascript:void(0)" onclick="window.open();"><?php echo $file->file_original_filename; ?></a><input type="button" value="X" onclick="if( confirm( 'Are you sure you want to permanently remove this file from the item as well as the archive?' ) ){ deleteFile( '<?php echo $file->getId(); ?>' )}"></li>
	<?php endforeach; ?>
	</ul>

</fieldset>
<input type="submit" value="Edit Item &gt;&gt;" name="item_edit" />

</form>
<form method="post" action="<?php echo $_link->to( 'items', 'delete'); ?>">
	<input type="hidden" value="<?php echo $item->getId(); ?>" name="item_id" />
	<input type="submit" value="Delete Item &gt;&gt;" name="item_delete" onclick="return confirm( 'Are you sure you want to delete this item, all of it\'s files, tags, and other data from the archive?' );"></input>
</form>