
function reveal(elem)
{
	if( document.getElementById(elem).style.display == 'none' )
	{
		Effect.BlindDown( elem, {duration: 1});	
	}
}

function hide(elem)
{
	if( document.getElementById(elem).style.display != 'none' )
	{
		Effect.BlindUp(elem, {duration: 0.6});
	}
}

function switchXbox( state, id )
{
	if( state )
	{
		document.getElementById(id).checked = false;
	}
}

function addFile()
{
	if (!document.getElementById('files')) return false;
	var filelist = document.getElementById('files');
	var input = document.createElement('li');
	//input.style.display = "none";
	filelist.appendChild( input );
	
	input.innerHTML = '<input name="objectfile[]" type="file" class="textinput" /><a href="javascript:void(0);" onclick="removeFile( parentNode )">Remove<\/a>';
	
	
	//Effect.Appear( input, {duration: 0.4} );
}

function removeFile( node )
{
//	Effect.Fade( node, {duration: 0.4} );
/*  setTimeout( function() { */document.getElementById('files').removeChild( node );/* }, 600);*/
}

/*function revealSwitch( field, file )
{
	new Ajax.Updater(	field,
						'<?php echo $_link->to() . DS; ?>' + file,
						{
						onComplete: function(t) {
							new Effect.BlindDown( field, {duration: 0.8} );
						}
						});
}*/