<div id="sf_admin_container">
	<h1>Page Dashboard</h1>
	
	<div id="sf_admin_content">
		<div class="sf_admin_list" style="width:300px;float: left;margin-right:50px;">
			<table>
				<thead>
					<tr>
						<th>Sites</th>
						<th>Page</th>
						<th>dl</th>
						<th>parsed</th>
						<th>quote</th>
					</tr>
				</thead>
				<?php $results = $q->fetchAll('SELECT `website`, count( id ) as count, sum( nb_citations ) as nb_citations, count( downloaded_date ) as downloaded, count( parsed_date ) as parsed FROM `page` GROUP BY `website`;'); ?>
				<?php foreach ($results as $key => $result): ?>
				<tr>
					<td><?php echo $result['website'];?></td>					
					<td><?php echo $result['count'] ?></td>
					<td><?php echo $result['downloaded'] ?></td>
					<td><?php echo $result['parsed'] ?></td>
					<td><?php echo $result['nb_citations'] ?></td>
				</tr>
				<?php endforeach;?>
			</table>
		</div>
	
		<div class="sf_admin_list" style="width:200px;float: left;">
			<table>
				<thead>
					<tr>
						<th colspan="2">HTTP Code</th>
					</tr>
				</thead>
				<?php $results = $q->fetchAll('SELECT `http_code`, count(id) as count FROM `page` GROUP BY `http_code`;'); ?>
				<?php foreach ($results as $key => $result): ?>
				<tr>
					<td><?php echo $result['http_code'];?></td>					
					<td><?php echo $result['count'] ?></td>
				</tr>
				<?php endforeach;?>
			</table>
		</div>
	
		<div class="clear"></div>
	</div>
	
</div>