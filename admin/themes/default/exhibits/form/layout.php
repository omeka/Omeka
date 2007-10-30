<?php head(array('title'=>'Exhibit Page', 'body_class'=>'exhibits')); ?>
<?php js('exhibits'); ?>
<div id="primary">
<?php common('exhibits-nav'); ?>

<script type="text/javascript" charset="utf-8">
//<![CDATA[

	Event.observe(window, 'load', makeLayoutSelectable);
	
	function makeLayoutSelectable() {
		$('layout-submits').hide();
		var current_layout = $('current_layout');
		var layouts = document.getElementsByClassName('layout');
		
		//Make each layout clickable
		layouts.each( function(layout) {
			layout.onclick = function() {
				//Make a copy of the image
				layouts.each(function(layout) {
					layout.style.border = "1px solid #ccc";
					layout.style.backgroundColor = "#fff";
				})
				this.style.border = "1px solid #6BA8DA";
				this.style.backgroundColor = "#A2C9E8"
				var img = this.getElementsByTagName('img')[0];
				var copy = img.cloneNode(true);
				var input = this.getElementsByTagName('input')[0];
				var title = input.readAttribute('value');
				var titletext = document.createTextNode(title);
				var heading = document.createElement('h2');
				heading.appendChild(titletext);
				
				//Overwrite the contents of the div that displays the current layout
				current_layout.update();
				current_layout.appendChild(copy);
				current_layout.appendChild(heading);
				$('layout-submits').show();
				//new Effect.Highlight(current_layout);

				//Make sure the input is selected
				var input = this.getElementsByTagName('input')[0];
				input.click();
			}
		});		
	}	

//]]>	
</script>

<form method="post" id="choose-layout">
		
		<?php 
		//	submit('Exhibit', 'exhibit_form');
		//	submit('New Page', 'page_form'); 
		?>
		
	
	<fieldset id="layouts">
		<legend>Layouts</legend>
		<div id="layout-thumbs">
		<?php 
			$layouts = get_ex_layouts();
	
			foreach ($layouts as $layout) {
				exhibit_layout($layout);
			} 
		?>
		</div>
		<div id="chosen_layout">
		<div id="current_layout"><p>Choose a layout by selecting a thumbnail on the right.</p></div>
		
		<p id="layout-submits">
		<button type="submit" name="choose_layout" id="choose_layout" class="page-button">Choose This Layout</button>
		or <button type="submit" name="cancel_and_section_form" id="section_form" class="cancel">Cancel</button></p>
	</div>
	</fieldset> 
	
</form>
</div>
<?php foot(); ?>