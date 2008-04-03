

	<div id="new-section-form">
	<div class="field"><?php text(array('name'=>'Section[title]', 'id'=>'title', 'class'=>'textinput'), $section->title, 'Section Title'); ?></div>
	<?php /* textarea(array('name'=>'Section[description]', 'id'=>'description', 'class'=>'textinput', 'rows'=>'10','cols'=>'40'), $section->description, 'Description for the Section'); */?>
	<div class="field"><?php text(array('name'=>'Section[slug]', 'class'=>'textinput', 'id'=>'section-slug'), $section->slug, 'URL Slug'); ?></div>
	<p><a id="add_section" href="javascript:void(0);">Add This Section</a> or <a id="cancel-add" href="javascript:void(0);">Cancel</a></p>
	
	</div>