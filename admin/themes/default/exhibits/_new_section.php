<div id="add-new-section-form">
	<fieldset>
	<div class="field"><?php text(array('name'=>'title', 'id'=>'title'), $section->title, 'Title for the Section'); ?></div>
	<div class="field"><?php textarea(array('name'=>'description', 'id'=>'description', 'rows'=>'10','cols'=>'40'), $section->description, 'Description for the Section'); ?></div>
	<div class="field"><?php text('slug', $section->slug, 'URL Slug (optional)'); ?></div>
	</fieldset>
	<fieldset>
		<p><a href="#">Add This Section</a> or <a href="#">Cancel</a></p>
	</fieldset>
</div>