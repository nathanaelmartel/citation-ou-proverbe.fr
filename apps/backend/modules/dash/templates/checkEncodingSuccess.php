<div id="sf_admin_container">
	<h1>Check Encoding</h1>
	
	<div id="sf_admin_content">
		<div class="sf_admin_list" >
			<table>
				<thead>
					<tr>
						<th>Site</th>
						<th>brut</th>
						<th>scraper::cleanAuthor</th>
						<th>scraper::cleanTag</th>
						<th>gamma</th>
						<th>epsilon</th>
					</tr>
				</thead>
				<?php foreach ($strings as $website => $string): ?>
					<tr>
						<td><?php echo $website ?></td>
						<td><?php echo $string ?></td>
						<td><?php echo scraper::cleanAuthor($string) ?></td>
						<td><?php echo scraper::cleanTag($string) ?></td>
						<td><?php echo scraper::encodingCorrection($string, 'gamma') ?></td>
						<td><?php echo scraper::encodingCorrection($string, 'epsilon') ?></td>
					</tr>
				<?php endforeach; ?>
			</table>
		</div>
	
</div>