<?php echo flash(); ?>
<div class="field" id="entity-type">
<?php radio('type', array('Institution'=>'Institution','Person'=>'Person'), not_empty_or($entity->type, $_POST['type'])); ?>
</div>
<div id="name-inputs">
<div class="field">
	<?php text(array('class' => 'textinput','name'=>'first_name','id'=>'first_name'), not_empty_or($entity->first_name, $_POST['first_name']), 'First Name'); ?>
</div>

<div class="field">
	<?php text(array('class' => 'textinput','name'=>'middle_name','id'=>'middle_name'), not_empty_or($entity->middle_name, $_POST['middle_name']), 'Middle Name'); ?>
</div>

<div class="field">
	<?php text(array('class' => 'textinput','name'=>'last_name','id'=>'last_name'), not_empty_or($entity->last_name, $_POST['last_name']), 'Last Name'); ?>
</div>
<div class="field">
	<?php text(array('class' => 'textinput','name'=>'institution','id'=>'institution'), not_empty_or($entity->institution, $_POST['institution']), 'Institution Name'); ?>
</div>
<div class="field">
	<?php text(array('class' => 'textinput','name'=>'email','id'=>'email'), not_empty_or($entity->email, $_POST['email']), 'Email'); ?>
</div>

<div class="field">
	<?php
	$institutions = institutions();
	select('parent_id', $institutions, not_empty_or($entity->parent_id, $_POST['parent_id']), 'Affiliation', 'id', 'name'); 
	?>
</div>
</div>

