<div id="sf_admin_container">
	<h1>Check Encoding</h1>
	
	<div id="sf_admin_content">
		<div class="sf_admin_list" >
			<table>
				<header>
					<tr>
						<th>Site</th>
						<th>mb_detect_encoding</th>
						<th>brut</th>
						<th>scraper::cleanAuthor</th>
						<th>scraper::cleanTag</th>
						<th>alpha</th>
						<th>beta</th>
						<th>gamma</th>
						<th>epsilon</th>
					</tr>
				</header>
				<?php foreach ($strings as $website => $string): ?>
					<tr>
						<td><?php echo $website ?></td>
						<td><?php echo mb_detect_encoding($string, 'UTF-8, ISO-8859-1') ?></td>
						<td><?php echo $string ?></td>
						<td><?php echo scraper::cleanAuthor($string) ?></td>
						<td><?php echo scraper::cleanTag($string) ?></td>
						<td><?php echo scraper::encodingCorrection($string, 'alpha') ?></td>
						<td><?php echo scraper::encodingCorrection($string, 'beta') ?></td>
						<td><?php echo scraper::encodingCorrection($string, 'gamma') ?></td>
						<td><?php echo scraper::encodingCorrection($string, 'epsilon') ?></td>
					</tr>
				<?php endforeach; ?>
			</table>
		</div>
	
</div>