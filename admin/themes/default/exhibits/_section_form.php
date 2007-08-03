<script type="text/javascript" charset="utf-8">
//<![CDATA[

//]]>	
</script>
	<fieldset>		
		<p><a id="add-section" href="javascript:void(0);">Add This Section</a> or <a id="cancel-add" href="javascript:void(0);">Cancel</a></p>
	</fieldset>
	<fieldset>
	<div class="field"><?php text(array('name'=>'title', 'id'=>'title'), $section->title, 'Title for the Section'); ?></div>
	<div class="field"><?php textarea(array('name'=>'description', 'id'=>'description', 'rows'=>'10','cols'=>'40'), $section->description, 'Description for the Section'); ?></div>
	<div class="field"><?php text('slug', $section->slug, 'URL Slug (optional)'); ?></div>
	</fieldset>
	