<div id="sf_admin_container">
	<h1>Citations Dashboard</h1>
	
	<div id="sf_admin_content">
		<div class="sf_admin_list" style="width:200px;float: left;margin-right:50px;">
			<table>
				<thead>
					<tr>
						<th colspan="2">Count</th>
					</tr>
				</thead>
				<tr>
					<td>Citations</td>
					<td><?php $total_citation = $q->fetchOne('SELECT COUNT(id) as count FROM citation'); echo $total_citation ?></td>
				</tr>
				<tr>
					<td>Auteurs</td>
					<td><?php $result = $q->fetchOne('SELECT COUNT(id) as count FROM author'); echo $result ?></td>
				</tr>
				<tr>
					<td>Tags</td>
					<td><?php $result = $q->fetchOne('SELECT COUNT(id) as count FROM tag'); echo $result ?></td>
				</tr>
			</table>
		</div>
		
		
		<div class="sf_admin_list" style="width:300px;float: left;">
			<table>
				<thead>
					<tr>
						<th>Day</th>
						<th>nb</th>
						<th>%</th>
						<th>graph</th>
					</tr>
				</thead>
				<?php $results = $q->fetchAll('SELECT SUBSTRING(created_at, 1, 10) as date, count( id ) as nb_citations FROM `citation` GROUP BY SUBSTRING(created_at, 1, 10);'); ?>
				<?php foreach ($results as $key => $result): ?>
				<tr>
					<td><?php echo $result['date'];?></td>					
					<td><?php echo $result['nb_citations'] ?></td>			
					<td><?php echo ceil($result['nb_citations']/$total_citation*100) ?></td>
					<td><div style="width:<?php echo ceil($result['nb_citations']/$total_citation*100) ?>%;background:#C64934;height:1em;"></div></td>
				</tr>
				<?php endforeach;?>
			</table>
		</div>
	
		<div class="clear"></div>
	</div>
	
</div>