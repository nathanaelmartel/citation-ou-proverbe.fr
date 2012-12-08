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
				<?php 
					$total_pages_downloaded = array(); 
					$total_pages_parsed = array(); 
					$results = $q->fetchAll('SELECT `website`, count( id ) as count, sum( nb_citations ) as nb_citations, count( downloaded_date ) as downloaded, count( parsed_date ) as parsed FROM `page` GROUP BY `website`;'); ?>
				<?php foreach ($results as $key => $result): 
					$total_pages_downloaded[$result['website']] = $result['downloaded']; 
					$total_pages_parsed[$result['website']] = $result['parsed']; ?>
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
		
		<?php foreach ($total_pages_downloaded as $website => $pages_downloaded): ?>
			<?php if ($pages_downloaded > 0): ?>
			<div class="sf_admin_list">
				<table>
					<thead>
						<tr>
							<th style="width:100px;"><?php echo $website?></th>
							<th style="width:100px;">downloaded</th>
							<th style="width:50px;"></th>
							<th style="min-width:100px;"></th>
							<th style="width:100px;">parsed</th>
							<th style="width:50px;"></th>
							<th style="min-width:100px;"></th>
						</tr>
					</thead>
					<?php $results = $q->fetchAll('SELECT SUBSTRING(parsed_date, 1, 10) as date, count( id ) as count FROM `page` WHERE website="'.$website.'" GROUP BY SUBSTRING(parsed_date, 1, 10);');
						foreach ($results as $key => $result_parsed) {
							$results_parsed[$result_parsed['date']] = $result_parsed['count'];
						}
					?>
					
					<?php $results_dl = $q->fetchAll('SELECT SUBSTRING(downloaded_date, 1, 10) as date, count( id ) as count FROM `page` WHERE website="'.$website.'" GROUP BY SUBSTRING(downloaded_date, 1, 10);'); ?>
					<?php foreach ($results_dl as $key => $result_dl): ?>
						<?php if ($result_dl['date'] != ''): ?>
							<tr>
								<td><?php echo $result_dl['date'];?></td>
								
								<td><?php echo $result_dl['count'] ?></td>			
								<td><?php echo ceil($result_dl['count']/$pages_downloaded*100) ?>%</td>
								<td><div style="width:<?php echo ceil($result_dl['count']/$pages_downloaded*100) ?>%;background:#C64934;height:1em;"></div></td>
								
								<td><?php echo $results_parsed[$result_dl['date']] ?></td>			
								<td><?php echo ceil($results_parsed[$result_dl['date']]/$total_pages_parsed[$website]*100) ?>%</td>
								<td><div style="width:<?php echo ceil($results_parsed[$result_dl['date']]/$total_pages_parsed[$website]*100) ?>%;background:#C64934;height:1em;"></div></td>
							</tr>
						<?php endif;?>
					<?php endforeach;?>
				</table>
			</div>
			<div class="clear"></div>
			<?php endif;?>
		<?php endforeach;?>
	</div>
	
</div>