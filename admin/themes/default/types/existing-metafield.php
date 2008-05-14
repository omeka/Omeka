<div class="field">
    <?php if($metafields): //If we have a list of metafields available, show a select box ?>
        <?php echo select( 
                array('name'=>'ExistingMetafields['.$index.'][metafield_id]', 'id'=>"metafield-$index"), 
                $metafields, 
                null, 
                'Choose an existing Metafield', 
                'id', 
                'name'); ?>
    <?php else: ?>            
        <?php echo text(array('name' => "ExistingMetafields[$index][name]"),$metafield->name); ?>    
    <?php endif; ?>
    <span>Remove this metafield from the Type</span> 
    <?php echo checkbox(array('name' => "remove_metafield[$index]")); ?>
    <span>Delete this metafield permanently</span>
    <?php echo checkbox(array('name' => "delete_metafield[$index]")); ?>
</div>