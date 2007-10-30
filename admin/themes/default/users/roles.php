<?php head(array('title'=>'User Roles', 'body_class'=>'users'));?>
<?php common('users-nav'); ?>

<script type="text/javascript" charset="utf-8">
/*
		function getRoleRuleForm(role) {
		var url = '<?php echo uri('users/rulesForm'); ?>?role='+role.value;
		new Ajax.Request(url, {
			method: 'post',
			onSuccess: function(req) {
				$('rulesForm').innerHTML = req.responseText;
			}
		});
	}
	
	Event.observe(window,'load',function(){
		Event.observe($('alter_role'),'change',function(event) {
			getRoleRuleForm(event.target);
		});
	});
*/	
</script>
<div id="primary">
<div id="message"></div>

<h1>Available Roles</h1>

<table id="userroles">
	<caption>User roles and permissions, in alphabetical order.</caption>
	<thead>
		<tr>
			<th></th>
			<th class="rolename">Super</th>
			<th class="rolename">Admin</th>
			<th class="rolename">Contributor</th>
			<th class="rolename">Researcher</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th>Collections</th>
			<td><img src="<?php img('tick.gif'); ?>" width="16" height="16" alt="yes" /></td>
			<td><img src="<?php img('tick.gif'); ?>" width="16" height="16" alt="yes" /></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<th>Entities</th>
			<td><img src="<?php img('tick.gif'); ?>" width="16" height="16" alt="yes" /></td>
			<td><img src="<?php img('tick.gif'); ?>" width="16" height="16" alt="yes" /></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<th>Exhibits</th>
			<td><img src="<?php img('tick.gif'); ?>" width="16" height="16" alt="yes" /></td>
			<td><img src="<?php img('tick.gif'); ?>" width="16" height="16" alt="yes" /></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<th>Files</th>
			<td><img src="<?php img('tick.gif'); ?>" width="16" height="16" alt="yes" /></td>
			<td><img src="<?php img('tick.gif'); ?>" width="16" height="16" alt="yes" /></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<th>Items</th>
			<td><img src="<?php img('tick.gif'); ?>" width="16" height="16" alt="yes" /></td>
			<td>limited</td>
			<td>limited</td>
			<td>limited</td>
		</tr>
		<tr>
			<th>Plugins</th>
			<td><img src="<?php img('tick.gif'); ?>" width="16" height="16" alt="yes" /></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<th>Settings</th>
			<td><img src="<?php img('tick.gif'); ?>" width="16" height="16" alt="yes" /></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<th>Tags</th>
			<td><img src="<?php img('tick.gif'); ?>" width="16" height="16" alt="yes" /></td>
			<td><img src="<?php img('tick.gif'); ?>" width="16" height="16" alt="yes" /></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<th>Themes</th>
			<td><img src="<?php img('tick.gif'); ?>" width="16" height="16" alt="yes" /></td>
			<td><img src="<?php img('tick.gif'); ?>" width="16" height="16" alt="yes" /></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<th>Types</th>
			<td><img src="<?php img('tick.gif'); ?>" width="16" height="16" alt="yes" /></td>
			<td><img src="<?php img('tick.gif'); ?>" width="16" height="16" alt="yes" /></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<th>Users</th>
			<td><img src="<?php img('tick.gif'); ?>" width="16" height="16" alt="yes" /></td>
			<td>limited</td>
			<td></td>
			<td></td>
		</tr>
	</tbody>
</table>

<?php if(1==0): //@since 9/17/07  Not supported through admin interface ?>
	<h3>Alter Role Permissions</h3>
	<?php select(array('name' => 'role', 'id'=>'alter_role'), $roles); ?>
	<div id="rulesForm"></div>
	</div>
<?php endif; ?>
</div>
<?php foot();?>