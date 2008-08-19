<?php head(array('title'=>'Browse Collections', 'body_class'=>'collections')); ?>
<div id="primary">

	<h1>Collections</h1>
	<?php if(!empty($collections)): ?>
	<a href="<?php echo uri('collections/add'); ?>" id="add-collection" class="add-collection">Add a Collection</a>
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
		<?php foreach($collections as $collection): ?>
		
		    <tr class="collection<?php if(++$key%2==1) echo ' odd'; else echo ' even'; ?>">
            	<td scope="row"><?php echo $collection->id;?>
                </td> 
            	<td class="title"><a href="<?php echo uri('collections/show/'.$collection->id); ?>"><?php echo h($collection->name); ?></a></td>
            	<td>
            	    <?php if(has_collectors($collection)): ?> 
            	    <ul>
    			
    			<?php foreach( $collection->Collectors as $k => $collector ): ?>
    			<li><?php echo h($collector->name); ?></li>
    			<?php endforeach; ?>
    			</ul>
    			<?php else: ?>
    			    <ul>
    			<li>No collectors</li>
    			</ul>
    			<?php endif; ?>
    			
    			</td>	
            	<td>	<?php if($time = $collection->added):?>
        		<?php echo date('m.d.Y', strtotime($time)); ?><?php endif; ?>
				</td>
				<td><a href="<?php echo uri('collections/edit/'.$collection->id); ?>" class="edit">Edit</a></td>
            </tr>
        
			

		<?php endforeach; ?>
		</tbody>
		</table>
	<?php else: ?>
	    <p>There are no collections in your archive. Why don't you <a href="<?php echo uri('collections/add'); ?>" >add one</a>?</p>
    	
	<?php endif; ?>
</div>		
<?php foot(); ?>
