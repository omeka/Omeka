<script type="text/javascript" charset="utf-8">
	
	const Person = "Person";
	const Institution = "Institution";
	
	function switchForm(radio) {
		var personElements = ['first_name','middle_name', 'last_name'];
		var institutionElements = ['institution'];
		
		if(radio.value == Institution) {
			//Disable name elements on form
			personElements.each(function(el) {
				$(el).disable();
			});
			institutionElements.each(function(el) {
				var element = $(el);
				element.enable();
			});
		}else{
			//Enable name elements
			personElements.each(function(el) {
				var element = $(el);
				element.enable();
			});
			institutionElements.each(function(el) {
				var element = $(el);
				element.disable();
			});
		}
	}
	
	Event.observe(window, 'load', function() {
		var radioButtons = $$("#entity-type input");

		for (var i=0; i < radioButtons.length; i++) {
			radioButtons[i].onclick = function() {
				switchForm(this);
			}
			if(radioButtons[i].checked) {
				switchForm(radioButtons[i]);
			}
		};
	});
</script>
<?php echo flash(); ?>
<fieldset>
	<legend>Add a Name</legend>
<div class="field" id="entity-type">
<?php radio('type', array('Institution'=>'Institution','Person'=>'Person'), $entity->type); ?>
</div>
<div class="field">
<?php text(array('name'=>'name', 'class'=>'textinput', 'id'=>'name'),$type->name, 'Type Name'); ?>
</div>
<div class="field">
	<?php text(array('class' => 'textinput','name'=>'first_name','id'=>'first_name'), $entity->first_name, 'First Name'); ?>
</div>

<div class="field">
	<?php text(array('class' => 'textinput','name'=>'middle_name','id'=>'middle_name'), $entity->middle_name, 'Middle Name'); ?>
</div>

<div class="field">
	<?php text(array('class' => 'textinput','name'=>'last_name','id'=>'last_name'), $entity->last_name, 'Last Name'); ?>
</div>

<div class="field">
	<?php text(array('class' => 'textinput','name'=>'email','id'=>'email'), $entity->email, 'Email'); ?>
</div>

<div class="field">
	<?php text(array('class' => 'textinput','name'=>'institution','id'=>'institution'), $entity->institution, 'Institution Name'); ?>
</div>

<div class="field">
	<?php
	$institutions = institutions();
	select('parent_id', $institutions, $entity->parent_id, 'Affiliation', 'id', 'name'); 
	?>
</div>
</fieldset>

