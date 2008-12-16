<?php head(array('title'=>'Plugins', 'content_class' => 'vertical-nav', 'bodyclass'=>'plugins primary')); ?>
<h1>Plugins</h1>

<?php common('settings-nav'); ?>


<div id="primary">
    <?php echo flash(); ?>
    
    <?php if ($plugins): ?>

<table id="plugin-info">
    <thead>
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Author</th>
            <th>Action</th>
            <th>Configure</th>
        </tr>
    </thead>
    <tbody>
<?php foreach( $plugins as $key => $plugin ): ?>
	<tr>
		<td><?php echo h($plugin->name); ?></td>
		<td><?php echo h($plugin->description);?></td>
		<td><?php echo h($plugin->author);?></td>
		<td>
        <?php 
        //If the plugin has been installed, then there should be separate forms for activation/configuration
        //I hate IE so much.  
        if($plugin->installed): ?>
            <?php if (has_permission('Plugins', 'activate')): ?>
                <form action="<?php echo uri('plugins/activate'); ?>" method="post" accept-charset="utf-8">
                    <button name="" type="submit" class="submit-medium" value="<?php echo $plugin->directory; ?>"><?php echo ($plugin->active) ? 'De-activate' : 'Activate'; ?>
                    </button>
                    <input type="hidden" name="activate" value="<?php echo $plugin->directory; ?>" />
                </form>                
            <?php endif; ?>
            <br />
            <?php if (has_permission('Plugins', 'uninstall')): ?>
                <form action="<?php echo uri(array(
                    'controller'=>'plugins', 
                    'action'=>'uninstall'), 'default'); ?>" method="post" accept-charset="utf-8">
                        <input type="submit" name="uninstall" class="uninstall submit-medium" value="Uninstall" />
                        <input type="hidden" name="name" value="<?php echo $plugin->directory; ?>" />
                </form>                
            <?php endif; ?>
        </td>
        <td>
        	<?php if ( has_permission('Plugins', 'config') and $plugin->has_config ): ?>
        		<a href="<?php echo uri('plugins/config', array('name'=>$plugin->directory)); ?>">Configure</a>
        	<?php endif; ?>
        </td>

    <?php else: //The plugin has not been installed yet ?>
        <?php if (has_permission('Plugins', 'install')): ?>
            <form action="<?php echo uri('plugins/install'); ?>" method="post" accept-charset="utf-8">     
                    <button name="" type="submit" class="submit-medium" value="<?php echo $plugin->directory; ?>">Install</button>
                    <input type="hidden" name="name" value="<?php echo $plugin->directory; ?>" />
            </form> 
        <?php endif; ?>
        </td>
        <td>
            &nbsp;
        </td>
        <td>
            &nbsp;
    <?php endif; ?>
		</td>
	</tr>
<?php endforeach; ?>
</tbody>
</table>

<?php else: ?>
<p>You don't have any plugins installed.  Add them to the plugins directory to see them listed here.</p>
<?php endif; ?>

<p class="manageplugins">Add new plugins by downloading them from the <a href="http://omeka.org/add-ons/plugins/">Omeka Plugins  Directory</a>, or <a href="http://omeka.org/codex/Plugin_Writing_Best_Practices">write your own</a>!</p>
</div>

<?php foot(); ?>