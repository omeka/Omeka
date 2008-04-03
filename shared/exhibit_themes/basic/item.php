<?php exhibit_head(); ?>
<div id="primary" class="show">

	<h1 class="item-title"><?php echo link_to_item($item); ?></h1>
	
<!--  The following is extended metadata that is assigned based on the Type that is assigned to an item -->
	
	<div id="extended-metadata">
	    <?php if(has_type($item)): ?>
	        <div id="item-type">
            <h2>Item Type</h2>
            <div class="field-value"><p><?php echo h($item->Type->name); ?></p></div>
            </div>
            
            <!-- This loop outputs all of the extended metadata -->
            <?php foreach( $item->TypeMetadata as $field => $text ): ?>
                <div id="<?php echo text_to_id($field); ?>" class="field">
                    <h2><?php echo h($field); ?></h2>
                    <div class="field-value"><?php echo nls2p(h($text)); ?></div>
                </div>
            <?php endforeach; ?>
            
	    <?php endif; ?>

	</div>
	
	<div id="item-metadata">

<!-- The following is dublin core metadata.  You can remove these fields if you do not wish
    to display that data on the public theme -->

	    <?php if($item->publisher): ?>
	        <div id="publisher" class="field">
            <h2>Publisher</h2>
            <div class="field-value"><?php echo nls2p(h($item->publisher)); ?></div> 
            </div>   
	    <?php endif; ?>
	
	    <?php if($item->creator): ?>
	        <div id="creator" class="field">
            <h2>Creator</h2>
            <div class="field-value"><?php echo nls2p(h($item->creator)); ?></div>
            </div>
	    <?php endif; ?>
	

        <?php if($item->description): ?>
            <div id="description" class="field">
            <h2>Description</h2>
            <div class="field-value"><?php echo nls2p(h($item->description)); ?></div>
            </div>
        <?php endif; ?>
	    	    
	    <?php if($item->relation): ?>
	        <div id="relation" class="field">
            <h2>Relation</h2>
            <div class="field-value"><?php echo nls2p(h($item->relation)); ?></div>
            </div>
	    <?php endif; ?>
	    
	    <?php if($item->spatial_coverage): ?>
	        <div id="spatial-coverage" class="field">
            <h2>Spatial Coverage</h2>
            <div class="field-value"><?php echo nls2p(h($item->spatial_coverage)); ?></div>
            </div>
	    <?php endif; ?>
	    
	    <?php if($item->rights): ?>
	        <div id="rights" class="field">
            <h2>Rights</h2>
            <div class="field-value"><?php echo nls2p(h($item->rights)); ?></div>
            </div>
	    <?php endif; ?>
	    
	    <?php if($item->source): ?>
	        <div id="source" class="field">
            <h2>Source</h2>
            <div class="field-value"><?php echo nls2p(h($item->source)); ?></div>
            </div>
	    <?php endif; ?>
	    
	    <?php if($item->subject): ?>
	        <div id="subject" class="field">
            <h2>Subject</h2>
            <div class="field-value"><?php echo nls2p(h($item->subject)); ?></div>
            </div>
	    <?php endif; ?>
	    
	    <?php if($item->additional_creator): ?>
	        <div id="additional-creator" class="field">
            <h2>Additional Creator</h2>
            <div class="field-value"><?php echo nls2p(h($item->additional_creator)); ?></div>
            </div>
	    <?php endif; ?>
	    
	    <?php if($item->format): ?>
	        <div id="format" class="field">
            <h2>Format</h2>
            <div class="field-value"><?php echo nls2p(h($item->format)); ?></div>
            </div>
	    <?php endif; ?>
	    
	    <?php if($item->contributor): ?>
	        <div id="contributor" class="field">
            <h2>Contributor</h2>
            <div class="field-value"><?php echo nls2p(h($item->contributor)); ?></div>
            </div>
	    <?php endif; ?>
	    
	    <?php if($item->rights_holder): ?>
	        <div id="rights-holder" class="field">
            <h2>Rights Holder</h2>
            <div class="field-value"><?php echo nls2p(h($item->rights_holder)); ?></div>
            </div>
	    <?php endif; ?>
	    
	    <?php if($item->provenance): ?>
	        <div id="provenance" class="field">
            <h2>Provenance</h2>
            <div class="field-value"><?php echo nls2p(h($item->provenance)); ?></div>
            </div>
	    <?php endif; ?>
	    
	    <?php if($item->date): ?>
	        <div id="date" class="field">
            <h2>Provenance</h2>
            <div class="field-value"><?php echo nls2p(date('m.d.Y', strtotime($item->date))); ?></div>
            </div>
	    <?php endif; ?>
	    
	    <?php if($item->temporal_coverage_start): ?>
	        <div id="temporal-coverage" class="field">
            <h2>Temporal Coverage</h2>
            <div class="field-value">
                <?php echo date('m.d.Y', strtotime($item->temporal_coverage_start)); ?> 
                - <?php echo date('m.d.Y', strtotime($item->temporal_coverage_end)); ?></div>
            </div>
	    <?php endif; ?>
	    
	    <div id="date-added" class="field">
        <h2>Date Added</h2>
        <div class="field-value"><?php echo nls2p(date('m.d.Y', strtotime($item->added))); ?></div>
	    </div>
	    
	    <?php if ( has_collection($item) ): ?>
    	    <div id="collection" class="field">
            <h2>Collection</h2>
            <div class="field-value"><p><?php echo link_to_collection($item->Collection); ?></p></div>
            </div>
    	<?php endif; ?>
	
	</div><!-- End Dublin Core metadata -->

	<div id="item-files">
		<?php echo display_files($item->Files); ?>
	</div>
	

	<?php if(count($item->Tags)): ?>
	<div class="tags">
		<h3>Tags:</h3>
		<?php echo tag_string($item->Tags, uri('items/browse/tag/'), "\n"); ?>	
	</div>
	<?php endif;?>
	
	<div id="citation" class="field">
    	<h2>Citation</h2>
    	<div id="citation-value" class="field-value"><?php echo nls2p($item->getCitation()); ?></div>
	</div>
</div>
<?php exhibit_foot(); ?>