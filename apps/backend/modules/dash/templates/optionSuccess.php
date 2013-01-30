<div id="sf_admin_container">
	<h1>Check Encoding</h1>
	
	<div id="sf_admin_content">
		<div class="sf_admin_list" >
			<table>
				<thead>
					<tr>
						<th>key</th>
						<th>brut</th>
						<th>scraper::cleanAuthor</th>
						<th>scraper::cleanTag</th>
						<th>gamma</th>
						<th>epsilon</th>
					</tr>
				</thead>
				<?php foreach ($options as $Option): ?>
					<tr>
						<td><?php echo $Option->option_key ?></td>
						<td><?php echo $Option->option_value ?></td>
						<td><?php echo scraper::cleanAuthor($Option->option_value) ?></td>
						<td><?php echo scraper::cleanTag($Option->option_value) ?></td>
						<td><?php echo scraper::encodingCorrection($Option->option_value, 'gamma') ?></td>
						<td><?php echo scraper::encodingCorrection($Option->option_value, 'epsilon') ?></td>
					</tr>
				<?php endforeach; ?>
			</table>
		</div>
	
</div>