<?php head(array('title'=>'User Roles', 'body_class'=>'users'));?>
<?php common('users-nav'); ?>

<script type="text/javascript" charset="utf-8">
    Event.observe(window,'load', function(){
        $$('#userroles li').invoke('observe', 'click', function(e){
            var form = this.select('form').first();
            var li = this;
            form.request({
               onComplete: function(t) {
                   li.update(t.responseText);
               },
               onFailure: function(t) {
                   alert(t.status);
               }
            });
        });
    });
</script>

<style type="text/css" media="screen">
    /* Override the form styling in screen.css */
    form {
        padding-bottom: 0;
    }
</style>
<div id="primary">
<div id="message"></div>

<h1>Available Roles</h1>

<table id="userroles">
<caption>User roles and available privileges for each role.  Click on an individual privilege to toggle it on/off.</caption>    
<thead>
    <tr>
        <th></th>
        <?php foreach ($roles as $role): ?>
            <th class="rolename"><?php echo Inflector::titleize($role); ?></th>
        <?php endforeach ?>
    </tr>
</thead>

<tbody>
    <?php foreach ($resources as $resource => $privileges): ?>
        <tr>
            <th><?php echo $resource; ?></th>
            <?php foreach ($roles as $role): ?>
                <td>
                    <ul>
                    <?php foreach ($privileges as $privilege): ?>
                        <li>
                            <?php $hasPermission = $acl->isAllowed($role, $resource, $privilege); ?>
                            <?php common('role-form', compact('hasPermission', 'privilege', 'resource', 'role'), 'users'); ?>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                </td>                
            <?php endforeach; ?>
        </tr>  
    <?php endforeach; ?>   
</table>

<?php if(1==0): //@since 9/17/07  Not supported through admin interface ?>
	<h3>Alter Role Permissions</h3>
	<?php select(array('name' => 'role', 'id'=>'alter_role'), $roles); ?>
	<div id="rulesForm"></div>
	</div>
<?php endif; ?>
</div>
<?php foot();?>