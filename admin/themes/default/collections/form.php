<?php echo flash(); ?>
<script type="text/javascript" charset="utf-8">
    /**
     * Clicking the [X] next to a collector's name removes it with AJAX
     **/
    Event.observe(window, 'load', function(){
         $$('a.remove-collector').invoke('observe', 'click', function(e){
            e.stop();
            var removeLink = this;
            new Ajax.Request(removeLink.href, {
               parameters: "output=json",
               onComplete: function(t) {
                   if(t.responseJSON['result'] == true) {
                       removeLink.up().destroy();
                   }
               } 
            });
        }); 
    });
</script>

<fieldset id="editcollection">
    <h2>Collection Details <span id="required-note">* Required Fields</span></h2>

<div class="field">
    <?php echo label(array('for' => 'name', 'class' => 'required'),'Collection Name'); ?>
    <div class="inputs">
        <?php echo text(array('name'=>'name', 'class'=>'textinput', 'id'=>'name', 'size'=>'40'),$collection->name); ?>
    </div>
<?php echo form_error('name'); ?>
</div>

<div class="field">
    <?php echo label(array('for' => 'description'),'Collection Description'); ?>
    
<?php echo form_error('description'); ?>
<div class="inputs">
<?php echo textarea(array('name'=>'description', 'class'=>'textinput', 'id'=>'description','rows'=>'10','cols'=>'60'),$collection->description); ?>
</div>
</div>

<h2>Collectors</h2>

<?php if (collection_has_collectors()): ?>
    <?php foreach( $collection->Collectors as $k => $collector ): ?>

    <ul id="collectors-list">
        <li>
        <?php echo html_escape($collector->getName()); ?>
        <a class="remove-collector" href="<?php echo html_escape(uri(
            array(
            'controller'=>'collections', 
            'action'=>'remove-collector', 
            'collector_id'=>$collector->id, 
            'collection_id'=>$collection->id), 'default')); ?>">Remove</a>
        </li>
    </ul>
    <?php endforeach; ?>
<?php else: ?>
    <p>This collection has no collectors.</p>
<?php endif; ?>

<div class="field">
<?php 
    echo label(array('for'=>'collectors'), 'Add a Collector (optional)'); ?>
    <div class="inputs">
        <div class="input">
    <?php echo select_entity(array('name'=>'collectors[]', 'id'=>'collector')); 
?>
</div></div>
</div>

<h2>Status: </h2>
<div class="field">
    <label for="public">Public</label>  
<?php 
    echo radio(array('name'=>'public'),array('0'=>'Not Public','1'=>'Public'), $collection->isPublic());
?>
</div>

<div class="field">
    <label for="featured">Featured</label>  
<?php 
    echo radio(array('name'=>'featured'),array('0'=>'Not Featured','1'=>'Featured'), $collection->isFeatured()); 
?>
</div>  

</fieldset>

<?php fire_plugin_hook('admin_append_to_collections_form', $collection); ?>