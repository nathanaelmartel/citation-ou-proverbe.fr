<div id="sf_admin_container">
	<h1>Check Encoding</h1>
	
	<div id="sf_admin_content">
		<div class="sf_admin_list" >
			<table>
				<thead>
					<tr>
						<th>key</th>
						<th>brut</th>
					</tr>
				</thead>
				<?php foreach ($options as $Option): ?>
					<tr>
						<td><?php echo $Option->option_key ?></td>
						<td><?php echo $Option->option_value ?></td>
					</tr>
				<?php endforeach; ?>
			</table>
		</div>
	
</div>