<div id="sf_admin_container">
	<h1>Citations Dashboard</h1>
	
	<?php 
	$object = array('citation', 'author', 'tag');
	$total = array();
	?>
	
	<div id="sf_admin_content">
		<div class="sf_admin_list" style="width:200px;float: left;margin-right:50px;">
			<table>
				<thead>
					<tr>
						<th colspan="2">Count</th>
					</tr>
				</thead>
				<?php foreach ($object as $obj): ?>
					<tr>
						<td><?php echo $obj ?></td>
						<td><?php $total[$obj] = $q->fetchOne('SELECT COUNT(id) as count FROM '.$obj); echo $total[$obj] ?></td>
					</tr>
				<?php endforeach;?>
				<tr>
					<td>source</td>
					<td><?php $total['source'] = $q->fetchOne('SELECT COUNT(id) as count FROM citation WHERE source <> \'\''); echo $total['source'] ?></td>
				</tr>
			</table>
		</div>
	
		<div class="clear"></div>
		
		<?php if (false) :?>
		<?php foreach ($object as $obj): ?>
			<div class="sf_admin_list" style="width:400px;margin-right:50px;float: left;">
				<table>
					<thead>
						<tr>
							<th style="width:100px;"><?php echo $obj;?></th>
							<th style="width:50px;"></th>
							<th style="width:50px;"></th>
							<th style="min-width:100px;"></th>
						</tr>
					</thead>
					<?php $results = $q->fetchAll('SELECT SUBSTRING(created_at, 1, 10) as date, count( id ) as nb FROM `'.$obj.'`  GROUP BY SUBSTRING(created_at, 1, 10);'); ?>
					<?php foreach ($results as $key => $result): ?>
						<tr>
							<td><?php echo $result['date'];?></td>					
							<td><?php echo $result['nb'] ?></td>			
							<td><?php echo ceil($result['nb']/$total[$obj]*100) ?>%</td>
							<td><div style="width:<?php echo ceil($result['nb']/$total[$obj]*100) ?>%;background:#C64934;height:1em;"></div></td>
						</tr>
					<?php endforeach;?>
				</table>
			</div>
		<?php endforeach;?>
		<?php endif;?>
	
		<div class="clear"></div>
	</div>
	
</div>