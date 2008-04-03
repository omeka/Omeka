//Adds an arbitrary number of file input elements to the items form so that more than one file can be uploaded at once
function filesAdding()
{
	
	if(!$('add-more-files')) return;
	if(!$('file-inputs')) return;
	if(!$$('#file-inputs .files')) return;
	var nonJsFormDiv = $('add-more-files');

	//This is where we put the new file inputs
	var filesDiv = $$('#file-inputs .files').first();
	
	var filesDivWrap = $('file-inputs');
	//Make a link that will add another file input to the page
	var link = document.createElement('a');
	var linkText = document.createTextNode('Add Another File');
	link.appendChild(linkText);
	link.href = "javascript:void(0)";
	link.id = "add-file";
	link.className = "add-file";

	Event.observe(link, 'click', function(){
		var inputs = $A(filesDiv.getElementsByTagName('input'));
		var inputCount = inputs.length;
		var fileHtml = '<div id="fileinput'+inputCount+'" class="fileinput"><input name="file['+inputCount+']" id="file['+inputCount+']" type="file" class="fileinput" /></div>';
		new Insertion.After(inputs.last(), fileHtml);
		$('fileinput'+inputCount).hide();
		new Effect.SlideDown('fileinput'+inputCount,{duration:0.2});
		//new Effect.Highlight('file['+inputCount+']');
	});

	nonJsFormDiv.update();
	
	filesDivWrap.appendChild(link);
}

Event.observe(window,'load',function() {
	if(!$('cancel_changes')) return;
	$('cancel_changes').onclick = function() {
		return confirm( 'Are you sure you want to cancel your changes? Anything you added will not be saved!' );
	};

});