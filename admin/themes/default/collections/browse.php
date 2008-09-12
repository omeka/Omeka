<?php head(array('title'=>'Browse Collections', 'body_class'=>'collections')); ?>
<h1>Collections</h1>
	<p id="add-collection" class="add-button"><a href="<?php echo uri('collections/add'); ?>" class="add-collection">Add a Collection</a></p>
<div id="primary">

	<?php if (has_collections()): ?>

		<table id="items" class="simple" cellspacing="0" cellpadding="0">
        	<thead>
        		<tr>
        		<th scope="col">ID</th>
        		<th scope="col">Name</th>
        		<th scope="col">Collectors</th>
        		<th scope="col">Date Added</th>
        		<th scope="col">Edit?</th>
        		</tr>
        	</thead>
        	<tbody>
        	    <?php $key = 0;?>
		<?php while (loop_collections()): ?>
		
		    <tr class="collection<?php if(++$key%2==1) echo ' odd'; else echo ' even'; ?>">
            	<td scope="row"><?php echo collection('id');?>
                </td> 
            	<td class="title"><?php echo link_to_collection(); ?></td>
            	<td>
            	<?php if (collection_has_collectors()): ?> 
            	    <ul>
            	        <li><?php echo collection('Collectors', array('delimiter'=>'</li><li>')); ?></li>
    			    </ul>
    			<?php else: ?>
    			    <ul>
    			        <li>No collectors</li>
    			    </ul>
    			<?php endif; ?>
    			
    			</td>	
            	<td><?php if($time = collection('Date Added')):?>
        		    <?php echo date('m.d.Y', strtotime($time)); ?>
        		<?php endif; ?>
				</td>
				<td>
				    <?php echo link_to_collection('Edit', array('class'=>'edit'), 'edit'); ?>
				</td>
            </tr>
        
			

		<?php endwhile; ?>
		</tbody>
		</table>
	<?php else: ?>
	    <p>There are no collections in your archive. Why don't you <a href="<?php echo uri('collections/add'); ?>" >add one</a>?</p>
    	
	<?php endif; ?>
</div>		
<?php foot(); ?>
