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
	<h2>Collection Details</h2>

<div class="field">
    <?php echo label(array('for' => 'name'),'Collection Name'); ?>
    <div class="input">
        <?php echo text(array('name'=>'name', 'class'=>'textinput', 'id'=>'name'),$collection->name); ?>
    </div>
<?php echo form_error('name'); ?>
</div>

<div class="field">
<?php echo form_error('description'); ?>
<?php echo textarea(array('name'=>'description', 'class'=>'textinput', 'id'=>'description','rows'=>'10'),$collection->description, 'Collection Description'); ?>
</div>

<?php if (collection_has_collectors()): ?>
	<h2>Collectors:</h2>
	<?php foreach( $collection->Collectors as $k => $collector ): ?>

	<ul id="collectors">
		<li>
		<?php echo h($collector->getName()); ?>
		<a class="remove-collector" href="<?php echo url_for(
		    array(
		    'controller'=>'collections', 
		    'action'=>'remove-collector', 
		    'collector_id'=>$collector->id, 
		    'collection_id'=>$collection->id), 'default'); ?>">[X]</a>
		</li>
	</ul>
	<?php endforeach; ?>
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
	echo radio(array('name'=>'public'),array('0'=>'Not Public','1'=>'Public'), $collection->public);
?>
</div>

<div class="field">
	<label for="featured">Featured</label>	
<?php 
	echo radio(array('name'=>'featured'),array('0'=>'Not Featured','1'=>'Featured'), $collection->featured); 
?>
</div>	

</fieldset>