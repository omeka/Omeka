<?php head(); ?>

	<div id="primary">
		
		<div id="exhibits" class="cbb">
			<h2>Exhibits</h2>

			<table cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<th scope="col"></th>
						<th scope="col" class="wide">Title</th>
						<th scope="col" class="wide">Tags</th>
					</tr>
				</thead>
				<tbody>
					<?php $exhibits = exhibits(); ?>
					<?php foreach( $exhibits as $key=>$exhibit ): ?>
						<tr class="exhibit <?php if($key%2==1) echo ' even'; else echo ' odd'; ?>">
							<td><?php echo $exhibit->id;?></td>
							<td><?php link_to_exhibit($exhibit); ?></td>
							<td><?php echo tag_string($exhibit, uri('exhibits/browse/tag/')); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			</div><!--end exhibits-->
	</div><!--end primary-->
	
	<div id="secondary">
		<?php common('sidebar'); ?>