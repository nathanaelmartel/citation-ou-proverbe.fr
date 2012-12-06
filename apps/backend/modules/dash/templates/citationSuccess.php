<div id="sf_admin_container">
	<h1>Citations Dashboard</h1>
	
	<div id="sf_admin_content">
		<div class="sf_admin_list" style="width:200px;float: left;">
			<table>
				<thead>
					<tr>
						<th colspan="2">Count</th>
					</tr>
				</thead>
				<tr>
					<td>Citations</td>
					<td><?php $result = $q->fetchOne('SELECT COUNT(id) as count FROM citation'); echo $result ?></td>
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
	
		<div class="clear"></div>
	</div>
	
</div>